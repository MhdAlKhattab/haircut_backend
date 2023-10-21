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

    public function addBarber(Request $request)
    {
        if (Auth::user()->role != 1){
            return response()->json(['message'=>'Access Denied, You Can Not Add Braber'], 403);
        }

        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $user = new User;

        if ($request['branch_id'])
            $user->branch_id = $request['branch_id'];

        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->phone_number = $request['phone_number'];
        $user->password = $request['password'];

        $user->save();

        return response()->json(['data' => $user], 200);
    }

    public function updateBarber(Request $request, $id)
    {
        if (Auth::user()->role != 1){
            return response()->json(['message'=>'Access Denied, You Can Not Add Braber'], 403);
        }

        $user = User::find($id);

        if(!$user){
            return response()->json(['errors' => 'There is no user with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'first_name' => 'string|max:255',
                'last_name' => 'string|max:255',
                'phone_number' => 'numeric|unique:users',
                'password' => 'string|min:8',
                'confirm_password' => 'string|same:password',   
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['first_name'])
            $user->first_name = $request['first_name'];
        if($request['last_name'])
            $user->last_name = $request['last_name'];
        if($request['phone_number'])
            $user->phone_number = $request['phone_number'];
        if($request['password'])
            $user->password = Hash::make($request['password']);

        $user->save();

        return response()->json(['data' => $user], 200);
    }

    public function addManager(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $user = new User;

        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->phone_number = $request['phone_number'];
        $user->password = $request['password'];
        $user->role = 1;

        $user->save();

        return response()->json(['data' => $user], 200);
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

        if ($user->branch_id) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => $user->role,
                'branch_id' => $user->branch_id,
            ], 200);
        }else {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => $user->role,
            ], 200);
        }
    }

    public function Logout() {
        $token = Auth::user()->token();
        $token->revoke();

        return response()->json(['message' => 'You Have Been Successfully Logged Out!'], 200);
    }
}
