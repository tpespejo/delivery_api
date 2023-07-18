<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //

    public function __construct(){
        $this->middleware('auth_api',[
            'except' => [
                'login',
                'register'
            ]
            ]);
    }

    public function register(Request $request){

        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email'=>'required|email|unique:users',
                'password'=>'required|min:6'
            ]);
    
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password']
            ]);
    
            $token = Auth::login($user);
    
            return response()->json([
                'message' => 'User successfully registered'
            ], 201);
    
             } catch(ValidationException $error){

                $errors = $error->errors();

                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $errors
                ], 400);
             }
    }   

    public function login(Request $request){
        $validatedData = $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);

        $credentials = $request->only('email','password');

        $token = Auth::attempt($credentials);

        if (!$token){
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'access_token' => $token
        ]);
    }
}
