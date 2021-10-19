<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login() 
    {
        $validated = Validator::make(request()->all(),[
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validated->errors(),
            ], 401);
        } 
        else 
        {
            $credential = request()->only(['email', 'password']);
            
            if(!$token = Auth::attempt($credential)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => 'User Create Successful',
                'token' => $token,
                'toktenType' => 'Bearer'
            ]);
        }
    }

    public function register() 
    {
        $validated = Validator::make(request()->all(),[
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        
        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validated->errors(),
            ], 401);
        } 
        else 
        {
            $user = User::create([
                'name' => request()->name,
                'email' => request()->email,
                'password' => Hash::make(request()->password)
            ]);

           return response()->json([
                'status' => true,
                'message' => 'User Create Successful',
                'data' => $user
            ]); 
        }
    }
}
