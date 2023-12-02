<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;
use App\Models\Employee_Info;
use App\Models\Branch;

class EmployeeController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'residence_number' => 'required|numeric|unique:employees',     
            'residence_expire_date' => 'required|date_format:Y-m-d',     
            'health_number' => 'required|numeric|unique:employees',     
            'health_expire_date' => 'required|date_format:Y-m-d',     
            'job' => 'required|string|max:255',
            'pay_type' => 'required|string|in:salary,commission,both',
            'salary' => 'required|numeric',
            'income_limit' => 'numeric',
            'commission' => 'numeric|min:1|max:100',
            'residence_cost' => 'required|numeric',
            'health_cost' => 'required|numeric',
            'insurance_cost' => 'required|numeric',
            'costs_responsible' => 'required|string|in:salon,self',
        ]);
    }

    public function addEmployee(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $employee = new Employee;
        
        $employee->branch_id = $request['branch_id'];
        $employee->name = $request['name'];
        $employee->residence_number = $request['residence_number'];
        $employee->residence_expire_date = $request['residence_expire_date'];
        $employee->health_number = $request['health_number'];
        $employee->health_expire_date = $request['health_expire_date'];
        $employee->job = $request['job'];
        $employee->pay_type = $request['pay_type'];
        $employee->salary = $request['salary'];
        if ($request['income_limit'])
            $employee->income_limit = $request['income_limit'];
        if ($request['commission'])
            $employee->commission = $request['commission'];
        $employee->residence_cost = $request['residence_cost'];
        $employee->health_cost = $request['health_cost'];
        $employee->insurance_cost = $request['insurance_cost'];
        $employee->costs_responsible = $request['costs_responsible'];

        $employee->save();

        $employee_info = Employee_Info::create([
            'employee_id' => $employee->id
        ]);
        $employee_info->save();

        return response()->json(['data' => $employee], 200);

        // Employee::with('Info')->where('id', '=', $employee->id)->first()
    }

    public function payCommission(Request $request)
    {
        $validatedData = Validator::make($request->all(),
            [
                'employee_id' => 'required|numeric|exists:employees,id',
                'amount' => 'required|numeric',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        $employee_info = Employee_Info::where('employee_id', '=', $request['employee_id'])->first();

        if ($request['amount'] > ($employee_info->total_commission - $employee_info->payed_commission))
            return response()->json(['errors' => 'The amount is greater than the remaining commission !'], 400);

        $employee_info->payed_commission += $request['amount'];
        $employee_info->save();

        return response()->json(['message' => 'You payed ' . $request['amount']], 200);
    }

    public function getEmployees($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->Employees, 200);
    }

    public function searchEmployees(Request $request, $branch_id)
    {
        $employees = Employee::where([
            ['branch_id', '=', $branch_id],
            ['name', 'LIKE', '%' . $request['query'] . '%'],
        ])->get();

        return response()->json($employees, 200);
    }

    public function getEmployeesInfo($branch_id)
    {
        $employees_info = Employee::where('branch_id', '=', $branch_id)
                                    ->select('id', 'name')
                                    ->with('Info')
                                    ->get();

        return response()->json($employees_info, 200);
    }

    public function searchEmployeesInfo(Request $request, $branch_id)
    {
        $employees_info = Employee::where([
                                        ['branch_id', '=', $branch_id],
                                        ['name', 'LIKE', '%' . $request['query'] . '%']
                                    ])
                                    ->select('id', 'name')
                                    ->with('Info')
                                    ->get();

        return response()->json($employees_info, 200);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::find($id);

        if(!$employee){
            return response()->json(['errors' => 'There is no employee with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'residence_number' => 'numeric|unique:employees',     
                'residence_expire_date' => 'date_format:Y-m-d',     
                'health_number' => 'numeric|unique:employees',     
                'health_expire_date' => 'date_format:Y-m-d',     
                'job' => 'string|max:255',
                'pay_type' => 'string|in:salary,commission,both',
                'salary' => 'numeric',
                'income_limit' => 'numeric',
                'commission' => 'numeric|min:1|max:100',
                'residence_cost' => 'numeric',
                'health_cost' => 'numeric',
                'insurance_cost' => 'numeric',
                'costs_responsible' => 'string|in:salon,self',
                'state' => 'boolean',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'])
            $employee->name = $request['name'];
        if($request['residence_number'])
            $employee->residence_number = $request['residence_number'];
        if($request['residence_expire_date'])
            $employee->residence_expire_date = $request['residence_expire_date'];
        if($request['health_number'])
            $employee->health_number = $request['health_number'];
        if($request['health_expire_date'])
            $employee->health_expire_date = $request['health_expire_date'];
        if($request['job'])
            $employee->job = $request['job'];
        if($request['pay_type'])
            $employee->pay_type = $request['pay_type'];
        if($request['salary'])
            $employee->salary = $request['salary'];
        if($request['income_limit'])
            $employee->income_limit = $request['income_limit'];
        if($request['commission'])
            $employee->commission = $request['commission'];
        if($request['residence_cost'])
            $employee->residence_cost = $request['residence_cost'];
        if($request['health_cost'])
            $employee->health_cost = $request['health_cost'];
        if($request['insurance_cost'])
            $employee->insurance_cost = $request['insurance_cost'];
        if($request['costs_responsible'])
            $employee->costs_responsible = $request['costs_responsible'];
        if($request['state'] != null)
            $employee->state = $request['state'];

        $employee->save();

        return response()->json(['data' => $employee], 200);
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::find($id);

        if(!$employee){
            return response()->json(['errors' => 'There is no employee with this id !'], 400);
        }

        $employee->delete();
        return response()->json(['message' => "Employee Deleted"], 200);
    }
}
