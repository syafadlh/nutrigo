<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Food;
use App\Models\FoodHistory;
use App\Models\Article;

class DashboardController extends Controller {

    public function index() {
        $stats = [
            'total_users'    => User::where('is_admin', false)->count(),
            'total_foods'    => Food::count(),
            'total_articles' => Article::count(),
            'total_logs'     => FoodHistory::whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::where('is_admin', false)->latest()->take(5)->get();

        $topFoods = FoodHistory::selectRaw('food_id, COUNT(*) as count')
            ->groupBy('food_id')
            ->orderByDesc('count')
            ->with('food')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats','recentUsers','topFoods'));
    }
}