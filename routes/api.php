<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashBoardController;
use App\Http\Controllers\Admin\BusController as AdminBusController;
use App\Http\Controllers\Admin\TripController as AdminTripController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewsController;
use App\Http\Controllers\Admin\OperationController as AdminOperationController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::post('register', [UserController::class, 'create']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'admin'], function () {

        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', [AdminDashBoardController::class, 'index']);
        });

        Route::group(['prefix' => 'customers'], function () {
            Route::get('/', [AdminCustomerController::class, 'index']);
        });

        Route::group(['prefix' => 'reviews'], function () {
            Route::get('', [AdminReviewsController::class, 'index']);
        });

        Route::group(['prefix' => 'buses'], function () {
            Route::get('/', [AdminBusController::class, 'index']);
            Route::post('/', [AdminBusController::class, 'index']);
            Route::get('search/{search}', [AdminBusController::class, 'search']);
            Route::post('create', [AdminBusController::class, 'store']);
            Route::get('/{id}', [AdminBusController::class, 'show']);
            Route::put('update/{id}', [AdminBusController::class, 'update']);
            Route::delete('delete/{id}', [AdminBusController::class, 'destroy']);
            Route::post('import', [AdminBusController::class, 'import']);
        });

        Route::group(['prefix' => 'operations'], function () {
            Route::get('/', [AdminOperationController::class, 'index']);
            Route::get('/{id}', [AdminOperationController::class, 'show']);
            Route::post('/create', [AdminOperationController::class, 'store']);
            Route::put('/update/{id}', [AdminOperationController::class, 'update']);
            Route::delete('delete/{id}', [AdminOperationController::class, 'destroy']);
        });


        Route::group(['prefix' => 'routes'], function () {
            Route::get('/', [AdminRouteController::class, 'index']);
            Route::post('/', [AdminRouteController::class, 'index']);
            Route::get('search/{search}', [AdminRouteController::class, 'search']);
            Route::post('create', [AdminRouteController::class, 'store']);
            Route::get('/{id}', [AdminRouteController::class, 'show']);
            Route::put('update/{id}', [AdminRouteController::class, 'update']);
            Route::delete('delete/{id}', [AdminRouteController::class, 'destroy']);
            Route::post('import', [AdminRouteController::class, 'import']);
        });

        Route::group(['prefix' => 'drivers'], function () {
            Route::get('/', [AdminDriverController::class, 'index']);
            Route::post('/', [AdminDriverController::class, 'index']);
            Route::get('search/{search}', [AdminDriverController::class, 'search']);
            Route::post('create', [AdminDriverController::class, 'store']);
            Route::get('/{id}', [AdminDriverController::class, 'show']);
            Route::put('update/{id}', [AdminDriverController::class, 'update']);
            Route::delete('delete/{id}', [AdminDriverController::class, 'destroy']);
            Route::post('import', [AdminDriverController::class, 'import']);
        });

        Route::group(['prefix' => 'trips'], function () {
            Route::get('/', [AdminTripController::class, 'index']);
            Route::post('/', [AdminTripController::class, 'index']);
            Route::get('search/{search}', [AdminTripController::class, 'search']);
            Route::post('create', [AdminTripController::class, 'store']);
            Route::get('/{id}', [AdminTripController::class, 'show']);
            Route::put('update/{id}', [AdminTripController::class, 'update']);
            Route::delete('delete/{id}', [AdminTripController::class, 'destroy']);
            Route::post('import', [AdminTripController::class, 'import']);
        });

        Route::group(['prefix' => 'schedules'], function () {
            Route::get('/', [ScheduleController::class, 'index']);
            Route::post('/', [ScheduleController::class, 'index']);
            Route::get('search/{search}', [ScheduleController::class, 'search']);
            Route::post('create', [ScheduleController::class, 'store']);
            Route::get('/{id}', [ScheduleController::class, 'show']);
            Route::put('update/{id}', [ScheduleController::class, 'update']);
            Route::delete('delete/{id}', [ScheduleController::class, 'destroy']);
            Route::post('import', [ScheduleController::class, 'import']);
        });

        Route::group(['prefix' => 'businesses'], function () {
            Route::get('', [BusinessController::class, 'index']);
            Route::post('create', [BusinessController::class, 'store']);
            Route::put('modify/{id}', [BusinessController::class, 'update']);
            Route::delete('delete/{id}', [BusinessController::class, 'destroy']);
        });

        Route::group(['prefix' => 'kyc'], function () {
            Route::post('/', [KycController::class, 'store']);
            Route::get('/', [KycController::class, 'index']);
            Route::put('/', [KycController::class, 'update']);
            Route::delete('/{kyc}', [KycController::class, 'destroy']);
        });
    });   

});
Route::group(['prefix' => 'auth'], function () {
    Route::post('verify', [AuthController::class, 'verify']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('changepassword', [AuthController::class, 'changePassword']);
    Route::post('forgetPassword', [AuthController::class, 'forgetPassword']);
    Route::get('login', function () {
        return response()->json(['message' => 'Unauthorized', 'status' => 401], Response::HTTP_UNAUTHORIZED);
    });
});









