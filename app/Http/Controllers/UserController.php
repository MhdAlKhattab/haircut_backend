<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function Me()
    {
        return response()->json([
            'user' => Auth::user()
        ], 200);
    }

    public function Update(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);

        $validatedData = Validator::make($request->all(),
            [
                'first_name' => 'string|max:255',
                'last_name' => 'string|max:255',
                'phone_number' => 'numeric|unique:users',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['first_name'] != null)
            $user->first_name = $request['first_name'];
        if($request['last_name'] != null)
            $user->last_name = $request['last_name'];
        if($request['phone_number'] != null)
            $user->phone_number = $request['phone_number'];

        $user->save();

        return response()->json(['data' => $user], 200);
    }
}
