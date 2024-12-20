<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function store(Request $request)
    {

        // Check if the user has the correct role (admin or super_admin)
        if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        //validate inputs
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255',
            'item_description' => 'required|string',
            'item_price' => 'required|numeric|min:0',
            'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_name' => 'required|string|exists:categories,category_name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //find the category
        $category = category::where('category_name', $request->category_name)->first();

        //store the image if provided
        $imagePath = null;
        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('item_images', 'public');
        }

        //create the item
        $item = Item::create([
            'item_name' => $request->item_name,
            'item_description' => $request->item_description,
            'item_price' => $request->item_price,
            'item_image' => $request->imagePath,
            'category_id' => $category->id,
            'is_deleted' => false,
        ]);

        return response()->json([
            'message' => 'Item added successfully',
            'item' => $item
        ], 201);

    }

    public function index(){

       // Check if the user is authenticated and has the correct role
    if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
        return response()->json(['message' => 'Forbidden'], 403);
    }
    
    //fetch all items where is_deleted=0
    $items=Item::where('is_deleted',false)
    ->with('category')
    ->get();

    return response()->json([
        'message'=>'Item retrieved successfully',
        'items'=>$items
    ],200);
    }
}
