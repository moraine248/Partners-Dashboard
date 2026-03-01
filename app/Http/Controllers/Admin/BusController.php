<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\ResourceInterface;
use App\Models\Bus;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class BusController extends Controller implements ResourceInterface
{
    
    public function index(Request $request):JsonResponse
    {
        $pagination = $request->input('pagination',  10);
        if ($request->isMethod('post')) {
            $request->session()->put('pagination', $pagination);
        } else {
            $pagination = 10;
        }
        $buses = Bus::where('user_id', Auth::id())->paginate($pagination);
        return response()->json($buses);
    }

    public function search($search):JsonResponse
    {
        $pagination = 10; 
        $buses = Bus::where('user_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('model', 'like', '%' . $search . '%')
                    ->orWhere('color', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orWhere('number', 'like', '%' . $search . '%')
                    ->orWhere('seats', 'like', '%' . $search . '%');
            })
            ->paginate($pagination);
        return response()->json($buses);
    }

    public function store(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'model' => 'required',
            'type' => 'required',
            'color' => 'required',
            'number' => 'required',
            'seats' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        $busData = array_merge($request->all(), ['user_id' => Auth::id()]);
        $bus = Bus::create($busData);
        return response()->json($bus, 201);
    }

    public function show($id):JsonResponse
    {

        $bus = Bus::findOrFail($id);
        
        if (!$bus) {
            return response()->json(['error' => 'Bus not found'], 404);
        }

        if ($bus->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to view this bus'], 403);
        }

        return response()->json($bus);
    }

    public function update(Request $request, $id):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'model' => 'required',
            'type' => 'required',
            'color' => 'required',
            'number' => 'required',
            'seats' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        
        $bus = Bus::where('id', $id)->first();

        if (!$bus) {
            return response()->json(['error' => 'Bus not found'], 404);
        }

        if ($bus->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this bus'], 403);
        }

        $bus = Bus::findOrFail($id);
        $bus->update($request->all());
        return response()->json($bus);
    }

    public function destroy($id):JsonResponse
    {
        $bus = Bus::findOrFail($id);
        

        if (!$bus) {
            return response()->json(['error' => 'Bus not found'], 404);
        }

        if ($bus->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to delete this bus'], 403);
        }
        $bus->delete();
        return response()->json(["message" => "deleted successfully"], 200);
    }

    public function import(Request $request, ExcelService $excelService):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $file = $request->file('file');
        $modelClassName = Bus::class; // Change to the appropriate model class name

        $result = $excelService->import($file, $modelClassName);

        if (isset($result['error'])) {
            return response()->json(['error' => 'Failed to import the file'], 500);
        }

        return response()->json(['success' => 'Import successful']);
    }
}
