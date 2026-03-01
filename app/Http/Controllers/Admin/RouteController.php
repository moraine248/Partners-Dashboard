<?php
namespace App\Http\Controllers\Admin;

use App\Models\Route;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $pagination = $request->input('pagination', 10);
        if ($request->isMethod('post')) {
            $request->session()->put('pagination', $pagination);
        } else {
            $pagination = 10;
        }
        $routes = Route::where('user_id', Auth::id())->paginate($pagination);
        return response()->json($routes);
    }


    public function search($search)
    {
        $pagination = 10;
        $routes = Route::where('user_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('from', 'like', '%' . $search . '%')
                    ->orWhere('to', 'like', '%' . $search . '%')
                    ->orWhere('user_id', 'like', '%' . $search . '%');
            })
            ->paginate($pagination);

        return response()->json($routes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        
        $routeData = array_merge($request->all(), ['user_id' => Auth::id()]);
        $route = Route::create($routeData);
        return response()->json($route, 201);
    }

    public function show($id)
    {
        $route = Route::findOrFail($id);
        return response()->json($route);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        

        $route = Route::findOrFail($id);

        if (!$route) {
            return response()->json(['error' => 'Route not found'], 404);
        }

        if ($route->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this Route'], 403);
        }

        $routeData = array_merge($request->all(), ['user_id' => Auth::id()]);
        $route->update($routeData);
        return response()->json(['status' => 200, 'message' => 'Updated Successfuly']);
    }

    public function destroy($id)
    {
        $route = Route::findOrFail($id);
        

        if (!$route) {
            return response()->json(['error' => 'Route not found'], 404);
        }

        if ($route->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this Route'], 403);
        }
        $route = Route::findOrFail($id);
        $route->delete();
        return response()->json(["message" => "deleted successfully"], 200);
    }

    public function import(Request $request, ExcelService $excelService)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $file = $request->file('file');
        $modelClassName = Route::class; // Change to the appropriate model class name

        $result = $excelService->import($file, $modelClassName);

        if (isset($result['error'])) {
            return response()->json(['error' => 'Failed to import the file'], 500);
        }

        return response()->json(['success' => 'Import successful']);
    }
}
