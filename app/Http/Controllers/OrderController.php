<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use App\Models\Promotion;

class OrderController extends Controller
{
    //remove selected items from checkout
    public function removeSelectedItems(Request $request)
    {
        // Ensure the user is logged in
        if (!Auth::check()) {
            return response()->json([
                'message' => 'You must be logged in to add to the cart.'
            ], 401);
        }

        // Get the user's cart
        $cart = $request->user()->cart;

        // If the cart doesn't exist
        if (!$cart) {
            return response()->json([
                'message' => 'You do not have a cart.'
            ], 400);
        }

        // Validate the cart_item_ids
        $request->validate([
            'cart_item_ids' => 'required|array',
            'cart_item_ids.*' => 'exists:cart_items,id',
        ]);

        // Update selected status to false for the specified cart items
        CartItem::where('cart_id', $cart->id)
            ->whereIn('id', $request->cart_item_ids)
            ->update(['selected' => false]);

        // Return a success response
        return response()->json([
            'message' => 'Selected items have been removed from checkout successfully.'
        ], 200);
    }

    //place an order
    public function placeOrder(Request $request)
    {
        $request->validate([
            'address' => 'required|String|max:255',
            'city' => 'required|String|max:50',
            'phone_number' => 'required|String|max:15',
            'postal_code' => 'required|String|max:15',
            'first_name' => 'required|String|max:255',
            'last_name' => 'required|String|max:255',
        ]);

        if (!Auth::user()) {
            return response()->json([
                'message' => 'You must be logged in tp place the order'
            ], 403);
        }

        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart) {
            return response()->json([
                'message' => 'Your cart is empty'
            ], 402);
        }

        $selectedItems = CartItem::with('item')
            ->where('cart_id', $cart->id)
            ->where('selected', true)
            ->get();

        if ($selectedItems->isEmpty()) {
            return response()->json([
                'message' => 'No items selected for checkout.'
            ], 403);
        }

        //calculate order sub totals
        $subtotal = $selectedItems->sum(function ($item) {
            return $item->quantity * $item->item->item_price;
        });

        $discount = 0;//set as default
        $categoryList = $selectedItems->pluck('item.category')->unique()->toArray();

        $promotions = Promotion::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('rules')
            ->get();

        foreach ($promotions as $promotion) {
            if (in_array($promotion->categories, $categoryList)) {
                foreach ($promotion->rules as $rule) {
                    if ($subtotal >= $rule->min_price) {
                        $discount = max($discount, $rule->discount_percentage);
                    }
                }
            }
        }

        $finalAmount = $subtotal - ($subtotal * $discount) / 100;

        DB::beginTransaction();

        try {
            //create the order
            $order = Order::create([
                'user_id' => $user->id,
                'order_date' => now(),
                'total_amount' => $subtotal,
                'final_amount' => $finalAmount,
                'discount' => $discount,
                'cart_id' => $cart->id,
                'order_status' => 'pending',
                'is_deleted' => false

            ]);

            //add order details
            OrderDetail::create([
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'phone_number' => $request->phone_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'order_id' => $order->id,
            ]);

            //add order items
            foreach ($selectedItems as $selectedItem) {
                OrderItem::create([
                    'quantity' => $selectedItem->quantity,
                    'order_id' => $order->id,
                    'item_id' => $selectedItem->item_id,
                    'item_price' => $selectedItem->item->item_price,
                ]);
            }

            //clear selected items from the cart
            CartItem::where('cart_id', $cart->id)
                ->whereIn('id', $selectedItems->pluck('id'))
                ->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully.',
                'order' => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place the order.Please try again.'
            ], 401);
        }
    }

    //get all orders for an admin
    public function getAllOrders(Request $request)
    {
        // Check if the user is authenticated and has the correct role
        /* if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
             return response()->json(['message' => 'Forbidden'], 403);
         }*/

        //fetch all items where is_deleted=0
        $orders = Order::where('is_deleted', false)
            ->with('items:item_name')
            ->get();

        // Format the response to include item names
        $ordersWithItems = $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'order_date' => $order->order_date,
                'total_amount' => $order->total_amount,
                'order_status' => $order->order_status,
                'items' => $order->items->map(function ($item) {
                    return [
                        'item_name' => $item->item_name,
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => 'orders retrieved successfully',
            'orders' => $ordersWithItems
        ], 200);
    }

    //view order  by id
    public function getOrderById($id)
    {
        //check if the user is authenticated and has the correct role
        /*if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
           return response()->json([
               'message' => 'Forbidden'
           ], 403);
       }*/

        //find the order by id and ensure it is not deleted
        $order = Order::where('id', $id)
            ->where('is_deleted', false)
            ->with('items:item_name')
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'order not found'
            ], 404);
        }

        // Format the response to include item names
        $formattedOrder = [
            'order_id' => $order->id,
            'order_date' => $order->order_date,
            'total_amount' => $order->total_amount,
            'order_status' => $order->order_status,
            'items' => $order->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                ];
            }),
        ];


        return response()->json([
            'message' => 'Order retrieved successfully',
            'item' => $formattedOrder
        ], 200);

    }

    //delete an order
    public function deleteOrder($id)
    {
        //check if the user is authenticated and has the correct role
        /*if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }*/

        //find the order by id 
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        //set the order as deleted
        $order->is_deleted = true;
        $order->save();

        return response()->json([
            'message' => 'Item marked as deleted successfully',
            'item' => $order
        ], 200);
    }

    //update the order status
    public function updateOrderStatus(Request $request, $id)
    {
        // Check if the user is authenticated and has the correct role
        /*if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }*/

        //validate the request input
        $request->validate([
            'order_status' => 'required|string|in:pending,successful,failed'
        ]);

        //Find the order by id and ensure it is not deleted
        $order = Order::where('id', $id)
            ->where('is_deleted', false)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        //update the order status
        $order->order_status = $request->order_status;
        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ], 201);
    }

    //get the order count according to the order status with total orders count
    public function getOrderStatusCount()
    {
        // Ensure the user is authenticated and has the correct role
        /*if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }*/

        //Fetch counts for each order status
        $statusCounts = Order::where('is_deleted', false)
            ->selectRaw('order_status,COUNT(*) as count')
            ->groupBy('order_status')
            ->pluck('count', 'order_status');

        //Get the total order count
        $totalOrders = Order::where('is_deleted', false)->count();


        //format the response
        $response = [
            'total_orders' => $totalOrders,
            'pending' => $statusCounts['Pending'] ?? 0,
            'successful' => $statusCounts['Successful'] ?? 0,
            'failed' => $statusCounts['Failed'] ?? 0,
        ];

        return response()->json([
            'message' => 'Order status count retrieved successfully.',
            'data' => $response
        ], 200);
    }

    //get the order status percentage
    public function getOrderStatusPercentage()
    {
        //Ensure the user is authenticated and has the correct role
        /* if(!Auth::check()|| !(Auth::user()->role=='admin'||Auth::user()->role=='super_admin')){
             return response()->json([
                 'message'=>'Forbidden'
             ],403);
         }*/

        //Fetch counts for each order status
        $statusCounts = Order::where('is_deleted', false)
            ->selectRaw('order_status,COUNT(*) as count')
            ->groupBy('order_status')
            ->pluck('count', 'order_status');

        //Get the total order count
        $totalOrders = Order::where('is_deleted', false)->count();

        //format the response with percentage
        $response = [
            'total_orders' => $totalOrders,
            'percentages' => [
                'pending' => $totalOrders > 0 ? round(($statusCounts['Pending'] ?? 0) / $totalOrders * 100, 2) : 0,
                'successful' => $totalOrders > 0 ? round(($statusCounts['Successful'] ?? 0) / $totalOrders * 100, 2) : 0,
                'failed' => $totalOrders > 0 ? round(($statusCounts['Failed'] ?? 0) / $totalOrders * 100, 2) : 0
            ]
        ];

        return response()->json([
            'message' => 'Order status percentages retrieved successfully.',
            'data' => $response
        ], 201);
    }

    public function getWeeklyOrderSummary()
    {
        // Ensure the user is authenticated and has the correct role
        /* if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }*/

        // Calculate start date for the last 4 weeks
        $today = now();
        $startDate = $today->copy()->subWeeks(4)->startOfWeek();

        // Fetch orders for last 4 weeks with their categories
        $weeklySummary = OrderItem::selectRaw("
        FLOOR(DATEDIFF(orders.order_date, ?) / 7) + 1 as week_number,
        categories.category_name as category_name,
        COUNT(order_items.id) as count
    ", [$startDate])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('items', 'items.id', '=', 'order_items.item_id')
            ->join('categories', 'categories.id', '=', 'items.category_id')
            ->where('orders.order_status', 'Successful')
            ->where('orders.order_date', '>=', $startDate)
            // Group only by week_number and category_name to avoid referencing non-grouped columns
            ->groupByRaw('week_number, categories.category_name')
            ->orderBy('week_number', 'asc')
            ->get()
            ->groupBy('week_number');

        // Format the response
        $response = [];
        foreach ($weeklySummary as $weekNumber => $data) {
            $categories = $data->mapWithKeys(function ($item) {
                return [$item->category_name => $item->count];
            });

            $response[] = [
                'week' => "Week $weekNumber",
                'categories' => $categories,
            ];
        }

        return response()->json([
            'message' => 'Weekly order summary retrieved successfully.',
            'data' => $response
        ], 200);

    }
}
