<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'numeric|exists:branches,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|numeric|unique:users',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password',
        ]);
    }

    public function AddBarber(Request $request)
    {
        if (Auth::user()->role != 1){
            return response()->json(['errors'=>'Access Denied, You Can Not Add Braber'], 403);
        }

        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $user = User::create([
            'branch_id' => $request['branch_id'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'phone_number' => $request['phone_number'],
            'password' => Hash::make($request['password']),
        ]);
        $user->save();

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => 0,
        ], 200);
    }

    public function AddManager(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'phone_number' => $request['phone_number'],
            'password' => Hash::make($request['password']),
            'role' => 1,
        ]);
        $user->save();

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => 1,
        ], 200);
    }

    public function Login(Request $request)
    {
        if(Auth::attempt($request->only('phone_number', 'password'))){

            $user = User::where('phone_number', $request['phone_number'])->firstOrFail();

        }else{
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $user->role,
        ], 200);
    }

    public function Logout() {
        $token = Auth::user()->token();
        $token->revoke();

        $response = ['message' => 'You have been successfully logged out!'];
        return response()->json($response, 200);
    }
}
