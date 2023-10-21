<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cashier_Withdraw;
use App\Models\Branch;

class CashierWithdrawController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'amount' => 'required|numeric',
            'statement' => 'required|string',
        ]);
    }

    public function addCashierWithdraw(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $withdraw = new Cashier_Withdraw;

        $withdraw->branch_id = $request['branch_id'];
        $withdraw->amount = $request['amount'];
        $withdraw->statement = $request['statement'];

        $withdraw->save();

        return response()->json(['data' => $withdraw], 200);
    }

    public function getCashierWithdraws($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Withdraws, 200);
    }

    public function updateCashierWithdraw(Request $request, $id)
    {
        $withdraw = Cashier_Withdraw::find($id);

        if(!$withdraw){
            return response()->json(['errors' => 'There is no withdraw with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'amount' => 'numeric',
                'statement' => 'string',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['amount'])
            $withdraw->amount = $request['amount'];
        if($request['statement'])
            $withdraw->statement = $request['statement'];
        
        $withdraw->save();

        return response()->json(['data' => $withdraw], 200);
    }

    public function deleteCashierWithdraw($id)
    {
        $withdraw = Cashier_Withdraw::find($id);

        if(!$withdraw){
            return response()->json(['errors' => 'There is no withdraw with this id !'], 400);
        }

        $withdraw->delete();
        
        return response()->json(['message' => "Withdraw Deleted"], 200);
    }
}
