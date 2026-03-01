<?php
namespace App\Http\Controllers\Admin;

use App\Models\Operation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        $pagination = $request->input('pagination', 10);
        $operations = Operation::where('user_id', Auth::id())->paginate($pagination);
        return response()->json($operations);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'route_id' => 'required',
            'bus_id' => 'required',
            'driver_id' => 'required',
            'numbers_of_seats' => 'required',
            'departure' => 'required|date',
            'amount' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $operationData = $request->all();
        
        $operationData = array_merge($operationData, ['user_id' => Auth::id()]);
        $operation = Operation::create($operationData);
        return response()->json($operation, 201);
    }

    public function show($id)
    {
        

        $operation = Operation::findOrFail($id);

        
        if (!$operation) {
            return response()->json(['error' => 'Operation not found'], 404);
        }

        if ($operation->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this business'], 403);
        }

        $operation = Operation::findOrFail($id);
        return response()->json($operation);
    }

    public function update(Request $request, $id)
    {
       $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);

       
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $operation = Operation::findOrFail($id);

        
        if (!$operation) {
            return response()->json(['error' => 'Operation not found'], 404);
        }

        if ($operation->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this operation'], 403);
        }

        $operation->update([
            'status' => $request->input('status')
        ]);
        return response()->json(["status" => 200, "message" => "Operation status Updated Successfully"]);
    }

    public function destroy($id)
    {
        $operation = Operation::findOrFail($id);

        
        if (!$operation) {
            return response()->json(['error' => 'Operation not found'], 404);
        }

        if ($operation->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this business'], 403);
        }
        $operation = Operation::findOrFail($id);
        $operation->delete();
        return response()->json(null, 204);
    }
}
