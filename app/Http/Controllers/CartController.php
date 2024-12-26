<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'message' => 'You must be logged in to add to the cart.'
            ], 401);
        }

        $user = Auth::user();

        //Check if the user already has a cart
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['added_at' => now()]
        );

        //check if the items already exists in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('item_id', $request->item_id)
            ->first();

        if ($cartItem) {
            //update the quantity if it already exists`            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            //Add new item to the cart
            CartItem::create([
                'cart_id' => $cart->id,
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Item added to cart successfully.'
        ], 201);
    }

    public function viewCart()
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'You must be logged in to view the cart.'], 401);
        }

        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)
            ->with(['items.item'])
            ->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty.'], 200);
        }

        return response()->json(['cart' => $cart], 200);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'message' => 'You must be logged in to remove the item'
            ], 401);
        }

        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'message' => 'No cart found for this user'
            ], 404);
        }

        $CartItem = CartItem::where('cart_id', $cart->id)
            ->where('item_id', $request->item_id)
            ->first();

        if (!$CartItem) {
            return response()->json([
                'message' => 'Item Not found in the cart'
            ], 404);
        }

        if ($request->has('quantity')) {
            //Reduce  quantity or remove item entirely if quantity becomes zero
            $CartItem->quantity -= $request->quantity;

            if ($CartItem->quantity <= 0) {
                $CartItem->delete();
                return response()->json([
                    'message' => 'Item removed from the cart successfully'
                ], 201);
            } else {
                $CartItem->save();
                return response()->json([
                    'Item quantity updated in the cart'
                ], 201);
            }
        } else {
            //remove item entirely
            $CartItem->delete();
            return response()->json([
                'message' => 'Item removed from the cart'
            ], 201);
        }
    }


    public function updateCartItemQuantity(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'message' => 'You must be logged in to update the cart item quantity.'
            ], 401);
        }

        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'message' => 'No cart found for this user.'
            ], 404);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('item_id', $request->item_id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Item not found in the cart.'
            ], 404);
        }

        //Update the quantity
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'Cart item quantity updated successfully',
            'cartItem' => $cartItem
        ], 200);
    }

    public function selectItems(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'You must be logged in to select cart items.'
            ], 401);
        }

        // Check if the user has a cart
        $cart = $request->user()->cart;

        if (!$cart) {
            return response()->json([
                'message' => 'You do not have a cart.'
            ], 400);
        }

        $request->validate([
            'cart_item_ids' => 'required|array',
            'cart_item_ids.*' => 'exists:cart_items,id',
        ]);

        //Mark all cart items for the current user as not selected
        CartItem::where('cart_id', $request->user()->cart->id)
            ->update(['selected' => false]);

        //update selected status for the provided cart items
        CartItem::whereIn('id', $request->cart_item_ids)
            ->update(['selected' => true]);

        return response()->json([
            'message' => 'Items selected successfully'
        ], 201);
    }

    public function fetchCheckoutItems(Request $request)
    {
        $cart = $request->user()->cart;

        if (!$cart) {
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }

        $selectedItems = CartItem::with('item') 
            ->where('cart_id', $cart->id)
            ->where('selected', true)
            ->get();

        if ($selectedItems->isEmpty()) {
            return response()->json(['message' => 'No items selected for checkout.'], 400);
        }

        // Calculate order summary
        $subtotal = $selectedItems->sum(function ($item) {
            return $item->quantity * $item->item->item_price;
        });

        $discount = 0; 
        $total = $subtotal - $discount;

        return response()->json([
            'selected_items' => $selectedItems,
            'order_summary' => [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
            ],
        ]);
    }

}
