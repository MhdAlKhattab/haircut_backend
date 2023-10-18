<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Branch;

class BranchController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_name' => 'required|string|max:255',
        ]);
    }

    public function addBranch(Request $request)
    {
        if (Auth::user()->role != 1){
            return response()->json(['message'=>'Access Denied, You Can Not Add Braber'], 403);
        }

        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $branch = Branch::create([
            'user_id' => Auth::user()->id,
            'branch_name' => $request['branch_name'],
        ]);
        $branch->save();

        return response()->json(['data' => $branch], 200);
    }

    public function getBranches()
    {
        $user = Auth::user();

        if ($user->role != 1){
            return response()->json(['message'=>'Access Denied, You Can Not Add Braber'], 403);
        }

        return response()->json($user->Branches, 200);
    }

    public function deleteBranch($id)
    {
        if (Auth::user()->role != 1){
            return response()->json(['message'=>'Access Denied, You Can Not Add Braber'], 403);
        }

        $branch = Branch::find($id);

        if(!$branch){
            return response()->json(['errors' => 'There is no branch with this id !'], 400);
        }

        $branch->delete();
        return response()->json(['message' => "Branch Deleted"], 200);
    }
}
