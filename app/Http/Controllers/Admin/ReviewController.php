<?php


namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $reviews = Review::whereHas('driver', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->orWhereHas('trip', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->paginate(20);

        return response()->json($reviews);
    }
}

