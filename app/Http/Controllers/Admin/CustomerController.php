<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Route;
use App\Models\Driver;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function index(){
        $userId = Auth::id();
        $customers = User::whereHas('bookings.trip', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['bookings.trip' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->select('name', 'email', 'phone')
        ->withCount(['bookings as completed_trips' => function ($query) {
            $query->where('status', 'completed');
        }])
        ->paginate(10);
    
     $customers->each(function ($customer) {
        $customer->total_price = $customer->bookings->sum(function ($booking) {
            return $booking->trip->price;
        });
    });

        return response()->json([
            'status' => 200,
            'customers' => $customers
        ]);
    }
}    