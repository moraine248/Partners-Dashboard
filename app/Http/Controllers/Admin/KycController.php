<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kyc;
use App\Services\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class KycController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'fullname' => 'required',
            'identity_type' => 'required',
            'identity_card' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $kycData = $request->all();
        $kycData['identity_card'] = (new ImageUpload())->upload($kycData['identity_card']);

        // Create a new Kyc record
        $kyc = Kyc::updateOrCreate(['user_id' => Auth::id()], $kycData);

        return response()->json([
            'status' => 201,
            'message' => 'Kyc record created successfully',
            'data' => $kyc,
        ]);
    }

    public function index()
    {
        $kycs = Kyc::where('user_id', Auth::id())->first();
        return response()->json([
            'status' => 200,
            'message' => 'Kyc records retrieved successfully',
            'data' => $kycs,
        ]);
    }

   

   
    public function update(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'fullname' => 'required',
            'identity_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $kycData = $request->all();

        if($request->hasFile($request->file('identity_card')))
            $kycData['identity_card'] = (new ImageUpload())->upload($kycData['identity_card']);

        // Update the Kyc record
        $kyc = Kyc::where('user_id', Auth::id())->update($kycData);

        return response()->json([
            'status' => 200,
            'message' => 'Kyc record updated successfully',
            'data' => $kyc,
        ]);
    }

    public function destroy(Request $request, Kyc $kyc)
    {
        // Check if the authenticated user ID matches the Kyc's user ID
        if (Auth::id() != $kyc->user_id) {
            return response()->json([
                'status' => 403,
                'message' => 'Access denied. You are not authorized to delete this Kyc record.',
            ], 403);
        }

        $kyc->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Kyc record deleted successfully',
        ]);
    }
}
