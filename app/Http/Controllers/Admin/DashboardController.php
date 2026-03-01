<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Route;
use App\Models\Driver;
use App\Models\Booking;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{

    public function index(){
        $userId = Auth::id();
        $customers = Booking::whereHas('trip', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->groupBy('user_id')
        ->select(DB::raw('COUNT(DISTINCT id) as count'))
        ->value('count');

        $drivers = Driver::where('user_id', Auth::id())->count();
        $routes = Route::where('user_id', Auth::id())->count();
        $bookingsCount = Booking::whereHas('trip', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->count();

        $bookings = Booking::whereHas('trip', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('created_at', '>=', Carbon::now()->subDays(7))
        ->groupBy(DB::raw('DATE(created_at)'))
        ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
        ->orderBy('date', 'asc')
        ->get();

        $wallets = (new WalletService())->getBalance(Auth::id());

        return response()->json([
            'customers' => $customers,
            'drivers' => $drivers,
            'routes' => $routes,
            'bookingsCount' => $bookingsCount,
            'bookings' => $bookings,
            'wallets' => $wallets,
        ]);
    }
}    