<?php
namespace App\Http\Controllers\Admin;

use App\Models\Driver;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $pagination = $request->input('pagination', 10);
        if ($request->isMethod('post')) {
            $request->session()->put('pagination', $pagination);
        } else {
            $pagination = 10;
        }
        $drivers = Driver::where('user_id', Auth::id())->paginate($pagination);
        return response()->json($drivers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'city' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'next_of_kin_name' => 'required',
            'next_of_kin_phone' => 'required',
            'next_of_kin_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        $driverData = array_merge($request->all(), ['user_id' => Auth::id()]);
        $driver = Driver::create($driverData);
        return response()->json($driver, 201);
    }

    public function show($id)
    {
        $driver = Driver::findOrFail($id);
        return response()->json($driver);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'city' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'next_of_kin_name' => 'required',
            'next_of_kin_phone' => 'required',
            'next_of_kin_address' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $driver = Driver::find($id);
        

        if (!$driver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }

        if ($driver->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this driver'], 403);
        }

        $driver = Driver::findOrFail($id);
        $driver->update($request->all());
        return response()->json($driver);
    }

    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        
        if (!$driver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }
        if ($driver->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to delete this driver'], 403);
        }    
        $driver->delete();
        return response()->json(null, 204);
    }

    public function search($search)
    {
        $pagination = 10;
        $drivers = Driver::where('user_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('next_of_kin_name', 'like', '%' . $search . '%')
                    ->orWhere('next_of_kin_phone', 'like', '%' . $search . '%')
                    ->orWhere('next_of_kin_address', 'like', '%' . $search . '%');
            })
            ->paginate($pagination);

            
        return response()->json($drivers);
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
        $modelClassName = Driver::class; // Change to the appropriate model class name

        $result = $excelService->import($file, $modelClassName);

        if (isset($result['error'])) {
            return response()->json(['error' => 'Failed to import the file'], 500);
        }

        return response()->json(['success' => 'Import successful']);
    }
}
