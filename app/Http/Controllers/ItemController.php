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

    //fetch all items
    public function index()
    {

        // Check if the user is authenticated and has the correct role
        if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        //fetch all items where is_deleted=0
        $items = Item::where('is_deleted', false)
            ->with('category')
            ->get();

        return response()->json([
            'message' => 'Item retrieved successfully',
            'items' => $items
        ], 200);
    }

    //view item by id
    public function show($id)
    {
        //check if the user is authenticated and has the correct role
        if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        //find the item by id and ensure it is not deleted
        $item = Item::where('id', $id)
            ->where('is_deleted', false)
            ->with('category')
            ->first();

        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Item retrieved successfully',
            'item' => $item
        ], 200);

    }

    public function delete($id)
    {
        //check if the user is authenticated and has the correct role
        if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        //find the item by id and ensure it exists
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        //set the item as deleted
        $item->is_deleted = true;
        $item->save();

        return response()->json([
            'message' => 'Item marked as deleted successfully',
            'item' => $item
        ], 200);
    }

    public function update(Request $request, $id)
    {

        // Check if the user is authenticated and has the correct role
        if (!Auth::check() || !(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        //validate the request data
        $validator = Validator::make($request->all(), [
            'item_name' => 'nullable|string|max:255',
            'item_description' => 'nullable|string',
            'item_price' => 'nullable|numeric|min:0',
            'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_name' => 'nullable|string|exists:categories,category_name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        //find the item by id
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        //if a new image is uploaded store it and update the path
        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('item_images', 'public');
            $item->item_image = $imagePath;
        }

        // Update the other fields if provided in the request
        if ($request->has('item_name')) {
            $item->item_name = $request->item_name;
        }

        if ($request->has('item_description')) {
            $item->item_description = $request->item_description;
        }

        if ($request->has('item_price')) {
            $item->item_price = $request->item_price;
        }

        if ($request->has('category_name')) {
            $category = Category::where('category_name', $request->category_name)->first();
            if ($category) {
                $item->category_id = $category->id;
            }
        }

        // Save the updated item
        $item->save();

        return response()->json([
            'message' => 'Item updated successfully',
            'item' => $item
        ], 200);
    }

    public function getItemsByCategory($category_name)
    {

        //Find the category by name
        $category = Category::where('category_name', $category_name)->first();

        if (!$category) {
            return response()->json([
                'message' => 'Category Not Found'
            ], 404);
        }

        //Fetch items where is_deleted=0 for the given category
        $items = Item::where('category_id', $category->id)
            ->where('is_deleted', false)
            ->get();

        return response()->json([
            'message' => 'Items retrieved successfully',
            'items' => $items
        ], 200);
    }
}
