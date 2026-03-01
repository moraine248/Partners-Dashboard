<?php

namespace App\Http\Controllers\Admin;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $pagination = $request->input('pagination', 10);
        if ($request->isMethod('post')) {
            $request->session()->put('pagination', $pagination);
        } else {
            $pagination = 10;
        }
        $trips = Schedule::where('user_id', Auth::id())->paginate($pagination);
        return response()->json($trips);
    }

    public function search($search)
    {
        $pagination = 10;
        $trips = Schedule::where(function ($query) use ($search) {
                $query->where('route_id', 'like', '%' . $search . '%')
                    ->orWhere('driver_id', 'like', '%' . $search . '%')
                    ->orWhere('user_id', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('departure', 'like', '%' . $search . '%')
                    ->orWhere('created_at', 'like', '%' . $search . '%')
                    ->orWhere('updated_at', 'like', '%' . $search . '%')
                    ->orWhere('bus_id', 'like', '%' . $search . '%');
            })
            ->paginate($pagination);
        return response()->json($trips);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_id' => 'required',
            'driver_id' => 'required',
            'user_id' => 'required',
            'price' => 'required',
            'departure' => 'required',
            'bus_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $trip = Schedule::create($request->all());
        return response()->json($trip, 201);
    }

    public function show($id)
    {
        $trip = Schedule::findOrFail($id);
        return response()->json($trip);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'route_id' => 'required',
            'driver_id' => 'required',
            'user_id' => 'required',
            'price' => 'required',
            'departure' => 'required',
            'bus_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $trip = Schedule::findOrFail($id);
        $trip->update($request->all());
        return response()->json($trip);
    }

    public function destroy($id)
    {
        $trip = Schedule::findOrFail($id);
        $trip->delete();
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
        $modelClassName = Schedule::class; // Change to the appropriate model class name

        $result = $excelService->import($file, $modelClassName);

        if (isset($result['error'])) {
            return response()->json(['error' => 'Failed to import the file'], 500);
        }

        return response()->json(['success' => 'Import successful']);
    }
}