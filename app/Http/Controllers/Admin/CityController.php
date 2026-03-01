<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use Illuminate\Http\JsonResponse;
use App\Contracts\ResourceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller implements ResourceInterface
{
    public function index(Request $request):JsonResponse
    {
        $pagination = $request->input('pagination',  10);
        if ($request->isMethod('post')) {
            $request->session()->put('pagination', $pagination);
        } else {
            $pagination = 10;
        }
        $cities = City::where('user_id', Auth::id())->paginate($pagination);
        return response()->json($cities);
    }

    public function store(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $city = City::create($request->all());
        return response()->json($city, 201);
    }

    public function show($id):JsonResponse
    {
        $city = City::findOrFail($id);
        
        if (!$city) {
            return response()->json(['error' => 'City not found'], 404);
        }

        if ($city->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to view this city'], 403);
        }

        return response()->json($city);
    }

    public function update(Request $request, $id):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $city = City::findOrFail($id);

        
        if (!$city) {
            return response()->json(['error' => 'City not found'], 404);
        }

        if ($city->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this city'], 403);
        }

        $city->update($request->all());
        
        return response()->json($city);
    }

    public function destroy($id):JsonResponse
    {
        $city = City::findOrFail($id);

        
        if (!$city) {
            return response()->json(['error' => 'City not found'], 404);
        }

        if ($city->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to delete this city'], 403);
        }

        $city->delete();
        return response()->json(null, 204);
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
        $modelClassName = City::class; // Change to the appropriate model class name

        $result = $excelService->import($file, $modelClassName);

        if (isset($result['error'])) {
            return response()->json(['error' => 'Failed to import the file'], 500);
        }

        return response()->json(['success' => 'Import successful']);
    }
}
