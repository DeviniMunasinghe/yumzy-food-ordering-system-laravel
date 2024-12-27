<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;

class OrderController extends Controller
{
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

        //calculate order totals
        $subtotal = $selectedItems->sum(function ($item) {
            return $item->quantity * $item->item->item_price;
        });

        $discount = 0;//set as default
        $finalAmount = $subtotal - $discount;

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
}
