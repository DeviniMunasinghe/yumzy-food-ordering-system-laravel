<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        //validate the request
        $request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        //check if the user exits
        $user=User::where('email',$request->email)->first();

        //if user not found or password doesn't match
        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email'=>['The provided credentials are incorrect'],
            ]);
        }

        //create a token for the user using sanctum
        $token=$user->createToken('yumzy')->plainTextToken;

        //return the token in the response
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'Bearer',
            'email'=>$user->email,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        //remove the user's token(Logout)
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);

    }
}
