<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;


class UserController extends Controller
{
    public function index(){
        $user=User::all();

        $data=[
           'status'=>200,
           'user'=>$user
        ];
        return response()->json($data,200);
    }

    public function register(Request $request){
        $validator=Validator::make($request->all(),
        [
           'email'=>'required|email',
           'username'=>'required',
           'password'=>'required'

        ]);

        if($validator->fails()){

            $data=[

                'status'=>422,
                'message'=>$validator->messages()

            ];
            return response()->json($data,422);
        }
        else{
            $user=new User;
            $user->email=$request->email;
            $user->username=$request->username;
            $user->password=$request->password;

            $user->save();

            $data=[

                'status'=>200,
                'message'=>'Data Uploaded successfully'
            ];

            return response()->json($data,200);
        }


    }
}
