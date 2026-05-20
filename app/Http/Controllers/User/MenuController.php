<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodHistory;
use App\Models\MenuRecommendation;
use App\Services\MenuRecommendationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller {

    public function __construct(private MenuRecommendationService $menuService) {}

public function index(Request $request) {
    $user     = Auth::user();
    $province = $request->get('province', $user->province);
    $mealType = $request->get('meal_type');
    $maxCal   = $request->get('max_cal');

    $foods = Food::where('is_active', true)
        ->when($province,  fn($q) => $q->where('origin', 'like', "%{$province}%"))
        ->when($mealType,  fn($q) => $q->where('meal_type', $mealType))
        ->when($maxCal,    fn($q) => $q->where('calories', '<=', (float)$maxCal))
        ->orderBy('name')
        ->paginate(12);

    $allergens     = $user->allergies->pluck('allergen')->toArray();
    $todayMenu     = $this->menuService->generateDailyMenu($user);
    $totalSelected = FoodHistory::where('user_id', $user->id)
        ->where('consumed_date', Carbon::today())
        ->sum('calories_consumed');

    return view('user.menu', compact(
        'user','foods','todayMenu','totalSelected','allergens','province'
    ));
}

    public function regenerate() {
        $user = Auth::user();
        MenuRecommendation::where('user_id', $user->id)
            ->where('recommendation_date', Carbon::today())
            ->delete();
        $this->menuService->generateDailyMenu($user);
        return back()->with('success', 'Menu berhasil diperbarui!');
    }

    public function logFood(Request $request) {
        $request->validate([
            'food_id'   => 'required|exists:foods,id',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        $food = Food::findOrFail($request->food_id);

        FoodHistory::create([
            'user_id'           => Auth::id(),
            'food_id'           => $food->id,
            'meal_type'         => $request->meal_type,
            'calories_consumed' => $food->calories,
            'consumed_date'     => Carbon::today(),
            'consumed_time'     => now()->format('H:i:s'),
        ]);

        return back()->with('success', "{$food->name} berhasil dicatat!");
    }
}