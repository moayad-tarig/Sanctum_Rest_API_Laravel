<?php

namespace App\Http\Controllers;

use App\Models\User;
use Facade\FlareClient\Http\Response;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $feilds = $request->validate([
           'name' => 'required|string',
           'email' => 'required|string|unique:users,email',
           'password' => 'required|string|confirmed' 
        ]);

        $user = User::create([
            'name' => $feilds['name'],
            'email' => $feilds['email'],
            'password' => bcrypt( $feilds['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return Response($response, 201);

    }


    public function login(Request $request)
    {
        $feilds = $request->validate([
            
            'email' => 'required|string',
            'password' => 'required|string' 
         ]);
         
         $user = User::where('email', $feilds['email'])->first();
         if(!$user && !Hash::check($feilds['password'], $user->password))
         {
            return response([
                'message' => 'Check Password Again'
            ],401);
         };

         
         $token = $user->createToken('myapptoken')->plainTextToken;
 
         $response = [
             'user' => $user,
             'token' => $token
         ];
         return Response($response, 201);
    }

    public function logout(Request $response)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'logged out'
        ];

    }
}
