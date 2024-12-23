<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    
    public function addAdmin(Request $request){
        //check if the authenticated user is a super admin
        if(!Auth::check()|| Auth::user()->role !== 'super_admin'){
            return response()->json([
                'message'=>'Forbidden'
            ],403);
        }

        //validate the input
        $validator =Validator::make($request->all(),[
            'email'=>'required|email|unique:users,email',
            'username'=>'required|string|max:225|unique:users,username',
            'password'=>'required|string|min:8',
            'user_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone_no'=>'nullable|string|max:15',
            'address'=>'nullable|string|max:255',
            'first_name'=>'required|string|max:255',
            'last_name'=>'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'errors'=>'$validator->errors()'
            ],422);
        }

        //Handle the user image upload if provided
        $imagePath=null;
        if($request->hasFile('user_image')){
            $imagePath = $request->file('user_image')->store('user_image','public');
        }

         //create a new admin 
         $admin=User::create([
            'email'=>$request->email,
            'username'=>$request->username,
            'password'=>Hash::make($request->password),
            'role'=>'admin',
            'user_image'=>$imagePath,
            'phone_no'=>$request->phone_no,
            'address' => $request->address,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
         ]);

         return response()->json([
            'message'=>'Admin created successfully',
            'admin'=>$admin
         ],201);

    }
}
