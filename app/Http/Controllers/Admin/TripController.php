<?php

namespace App\Http\Controllers\Admin;

use App\Models\Trip;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
 /**
     * Display a paginated list of trips for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request):JsonResponse
    {
        $pagination = $request->input('pagination', 10);
        if ($request->isMethod('post')) {
            $request->session()->put('pagination', $pagination);
        } else {
            $pagination = 10;
        }
        $trips = Trip::where('user_id', Auth::id())->paginate($pagination);
        return response()->json($trips); // Returns an object (Paginated list of trips).
    }

    /**
     * Search for trips based on the provided search query.
     *
     * @param  string  $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($search)
    {
        $pagination = 10;
        $trips = Trip::where(function ($query) use ($search) {
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
        return response()->json($trips); // Returns an object (Paginated list of trips based on the search query).
    }

    /**
     * Store a newly created trip resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            return response()->json($validator->errors(), 400); // Returns an object (Validation errors).
        }

        $trip = Trip::create($request->all());
        return response()->json($trip, 201); // Returns an object (Created trip resource).
    }

    /**
     * Display the specified trip resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $trip = Trip::findOrFail($id);
        return response()->json($trip); // Returns an object (Specific trip resource).
    }

    /**
     * Update the specified trip resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id):JsonResponse
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
        $trip = Trip::findOrFail($id);

        
        if (!$trip) {
            return response()->json(['error' => 'Trip not found'], 404);
        }

        if ($trip->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this trip'], 403);
        }

        $trip = Trip::findOrFail($id);
        $trip->update($request->all());
        return response()->json(['status' => 200, "message" => "deleted Successfully"]); 
    }

    /**
     * Remove the specified trip resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id):JsonResponse
    {
        $trip = Trip::findOrFail($id);

        
        if (!$trip) {
            return response()->json(['error' => 'Trip not found'], 404);
        }
        if ($trip->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to delete this trip'], 403);
        }
        $trip->delete();
        return response()->json(["message" => "deleted successfully"], 200); 
    }

    public function import(Request $request, ExcelService $excelService) :JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $file = $request->file('file');
        $modelClassName = Trip::class; // Change to the appropriate model class name

        $result = $excelService->import($file, $modelClassName);

        if (isset($result['error'])) {
            return response()->json(['error' => 'Failed to import the file'], 500);
        }

        return response()->json(['success' => 'Import successful']);
    }
}