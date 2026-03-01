<?php
namespace App\Http\Controllers\Admin;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use Illuminate\Http\JsonResponse;
use App\Contracts\BusinessInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller implements BusinessInterface
{
    public function index():JsonResponse
    {
        
        $businesses = Business::where('user_id', Auth::id())->first();
        return response()->json($businesses);
    }

    public function store(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'country' => 'required|string',
            'state' => 'required|string',
            'phone' => 'required|string',
            'bank_name' => 'required|string',
            'account_number' => 'required|integer',
            'email' => 'required|email',
            'service' => 'required|string',
            'website' => 'nullable|url',
            'linkedin' => 'nullable|url',
        ]);
    
        
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        $businessData = array_merge($request->all(), ['user_id' => Auth::id()]);
        if ($request->hasFile('logo')) {
            $businessData['logo'] = (new ImageUpload())->upload($request->file('logo'));
        }
        Business::create($businessData);
        return response()->json(['message' => 'Business created successfully', 'staus' => 200]);
    }

    public function show($id):JsonResponse
    {
        $bus = Business::findOrFail($id);
        
        if (!$bus) {
            return response()->json(['error' => 'Business not found'], 404);
        }

        if ($bus->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to view this Business'], 403);
        }

        return response()->json($bus);
    }

    public function update(Request $request, $id):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'address' => 'string',
            'country' => 'string',
            'state' => 'string',
            'phone' => 'string',
            'bank_name' => 'string',
            'account_number' => 'integer',
            'email' => 'email',
            'service' => 'string',
            'website' => 'nullable|url',
            'linkedin' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        
        $business = Business::where('user_id', $id)->first();

        if (!$business) {
            return response()->json(['error' => 'Business not found'], 404);
        }

        if ($business->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this business'], 403);
        }

        $businessData = $request->all();
        if ($request->hasFile('logo')) {
            $businessData['logo'] = (new ImageUpload())->upload($request->file('logo'));
        }

        $business->update($businessData);
        return response()->json(['message' => 'Business updated successfully']);
    }

    public function destroy($id):JsonResponse
    {
        
        $business = Business::where('user_id', $id)->first();

        if (!$business) {
            return response()->json(['error' => 'Business not found'], 404);
        }

        if ($business->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to delete this business'], 403);
        }

        $business->delete();
        return response()->json(['message' => 'Business deleted successfully']);
    }
}
