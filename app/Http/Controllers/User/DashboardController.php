<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\FoodHistory;
use App\Services\MenuRecommendationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller {

    public function __construct(private MenuRecommendationService $menuService) {}

    public function index() {
        $user          = Auth::user();
        $today         = Carbon::today();
        $recommendation = $this->menuService->generateDailyMenu($user);

        $todayCalories = FoodHistory::where('user_id', $user->id)
            ->where('consumed_date', $today)
            ->sum('calories_consumed');

        $weeklyHistory = FoodHistory::where('user_id', $user->id)
            ->whereBetween('consumed_date', [$today->copy()->subDays(6), $today])
            ->selectRaw('consumed_date, SUM(calories_consumed) as total')
            ->groupBy('consumed_date')
            ->orderBy('consumed_date')
            ->get();

        $articles = Article::where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        $reminders = $user->reminders()->where('is_active', true)->get();
        $unreadNotifications = $user->notifications()->where('is_read', false)->count();

        return view('user.dashboard', compact(
            'user','recommendation','todayCalories',
            'weeklyHistory','articles','reminders','unreadNotifications'
        ));
    }
}