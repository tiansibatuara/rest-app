<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $input = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
        ]);
        $token = $user->createToken('UDSKY77')->plainTextToken;
        $data = [
            'status' => Response::HTTP_CREATED,
            'message' => 'User created',
            'data' => $user,
            'token' => $token,
            'type' => 'Bearer',
        ];
        return response()->json($data, Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $input = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $input['email'])->first();

        if (!$user || !Hash::check($input['password'], $user->password)) {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('UDSKY77')->plainTextToken;
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Login success',
            'data' => $user,
            'token' => $token,
            'type' => 'Bearer',
        ];
        return response()->json($data, Response::HTTP_OK);
    }

    public function user()
    {
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Detail user',
            'data' => auth()->user(),
        ];
        return response()->json($data, Response::HTTP_OK);
    }

    public function logout() {
        auth()->user()->tokens->each(function ($token){
            $token->delete();
        });
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Logout success',
        ];
        return response()->json($data, Response::HTTP_OK);
    }
}
