<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\General_Service_Term;
use App\Models\Branch;

class GeneralServiceTermController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => 'required|numeric|exists:branches,id',
            'name' => 'required|string|max:255',
            'tax_state' => 'required|boolean',
        ]);
    }

    public function addTerm(Request $request)
    {
        $validatedData = $this->validator($request->all());
        if ($validatedData->fails())  {
            return response()->json(['errors'=>$validatedData->errors()], 400);
        }

        $term = new General_Service_Term;

        $term->branch_id = $request['branch_id'];
        $term->name = $request['name'];
        $term->tax_state = $request['tax_state'];

        $term->save();

        return response()->json(['data' => $term], 200);
    }

    public function getTerms($branch_id)
    {
        $branch = Branch::find($branch_id);

        return response()->json($branch->General_Service_Terms, 200);
    }

    public function getUntaxedTerms($branch_id)
    {
        $terms = General_Service_Term::where([
            ['branch_id', '=', $branch_id],
            ['tax_state', '=', 0]
        ])->get();

        return response()->json($terms, 200);
    }

    public function gettaxedTerms($branch_id)
    {
        $terms = General_Service_Term::where([
            ['branch_id', '=', $branch_id],
            ['tax_state', '=', 1]
        ])->get();

        return response()->json($terms, 200);
    }

    public function updateTerm(Request $request, $id)
    {
        $term = General_Service_Term::find($id);

        if(!$term){
            return response()->json(['errors' => 'There is no term with this id !'], 400);
        }

        $validatedData = Validator::make($request->all(),
            [
                'name' => 'string|max:255',
                'tax_state' => 'boolean',
            ]
        );

        if($validatedData->fails()){
            return response()->json(["errors"=>$validatedData->errors()], 400);
        }

        if($request['name'])
            $term->name = $request['name'];
        if($request['tax_state'])
            $term->tax_state = $request['tax_state'];

        $term->save();

        return response()->json(['data' => $term], 200);
    }

    public function deleteTerm($id)
    {
        $term = General_Service_Term::find($id);

        if(!$term){
            return response()->json(['errors' => 'There is no term with this id !'], 400);
        }

        $term->delete();
        return response()->json(['message' => "Term Deleted"], 200);
    }
}
