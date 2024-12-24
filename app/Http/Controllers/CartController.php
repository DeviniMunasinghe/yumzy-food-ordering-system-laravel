<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $request->validate([
            'item_id'=>'required|exists:items,id',
            'quantity'=>'required|integer|min:1',
        ]);

        if(!Auth::check()){
            return response()->json([
                'message'=>'You must be logged in to add to the cart.'
            ],401);
        }

        $user =Auth::user();

        //Check if the user already has a cart
        $cart = Cart::firstOrCreate(
            ['user_id'=>$user->id],
            ['added_at'=>now()]
        );

        //check if the items already exists in the cart
        $cartItem=CartItem:: where('cart_id',$cart->id)
        ->where('item_id',$request->item_id)
        ->first();

        if($cartItem){
            //update the quantity if it already exists
            $cartItem->quantity+=$request->quantity;
            $cartItem->save();
        }else{
            //Add new item to the cart
            CartItem::create([
                'cart_id'=>$cart->id,
                'item_id'=>$request->item_id,
                'quantity'=>$request->quantity,
            ]);
        }

        return response()->json([
            'message'=>'Item added to cart successfully.'
        ],201);
    }

    public function viewCart(){
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
}
