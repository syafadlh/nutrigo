<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodHistory;
use App\Models\MenuRecommendation;
use App\Models\Notification;
use App\Services\MenuRecommendationService;
use App\Services\CalorieCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuApiController extends Controller
{
    public function __construct(
        private MenuRecommendationService $menuService,
        private CalorieCalculatorService  $calorieService
    ) {}

    // ── GET /api/user/calorie-summary ─────────────────────────
    public function calorieSummary()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        $consumed = FoodHistory::where('user_id', $user->id)
            ->where('consumed_date', $today)
            ->sum('calories_consumed');

        $target    = $user->daily_calorie_needs ?? 2000;
        $remaining = max(0, $target - $consumed);
        $pct       = min(100, round(($consumed / $target) * 100, 1));

        return response()->json([
            'target'    => $target,
            'consumed'  => round($consumed, 1),
            'remaining' => round($remaining, 1),
            'percent'   => $pct,
            'status'    => $consumed > $target ? 'over' : ($consumed > $target * 0.8 ? 'near' : 'ok'),
        ]);
    }

    // ── POST /api/menu/regenerate ─────────────────────────────
    public function regenerateMenu()
    {
        $user = Auth::user();
        MenuRecommendation::where('user_id', $user->id)
            ->where('recommendation_date', Carbon::today())
            ->delete();

        $menu = $this->menuService->generateDailyMenu($user);
        $menu->load(['breakfast', 'lunch', 'dinner']);

        return response()->json([
            'success' => true,
            'menu'    => [
                'breakfast' => $this->formatFood($menu->breakfast),
                'lunch'     => $this->formatFood($menu->lunch),
                'dinner'    => $this->formatFood($menu->dinner),
                'total_calories' => $menu->total_calories,
            ],
        ]);
    }

    // ── POST /api/menu/log ────────────────────────────────────
    public function logFood(Request $request)
    {
        $request->validate([
            'food_id'   => 'required|exists:foods,id',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        $food = Food::findOrFail($request->food_id);
        $user = Auth::user();

        $history = FoodHistory::create([
            'user_id'           => $user->id,
            'food_id'           => $food->id,
            'meal_type'         => $request->meal_type,
            'calories_consumed' => $food->calories,
            'consumed_date'     => Carbon::today(),
            'consumed_time'     => now()->format('H:i:s'),
        ]);

        // Recalculate summary
        $consumed = FoodHistory::where('user_id', $user->id)
            ->where('consumed_date', Carbon::today())
            ->sum('calories_consumed');

        // Buat notifikasi jika kalori melebihi target
        if ($consumed > ($user->daily_calorie_needs ?? 2000)) {
            Notification::firstOrCreate(
                [
                    'user_id'    => $user->id,
                    'type'       => 'warning',
                    'created_at' => Carbon::today(),
                ],
                [
                    'title'   => '⚠️ Kalori harian terlampaui!',
                    'message' => "Kalori kamu hari ini sudah " . number_format($consumed) . " kcal, melebihi target " . number_format($user->daily_calorie_needs) . " kcal.",
                    'is_read' => false,
                ]
            );
        }

        return response()->json([
            'success'  => true,
            'message'  => "{$food->name} berhasil dicatat!",
            'food'     => $this->formatFood($food),
            'consumed' => round($consumed, 1),
            'target'   => $user->daily_calorie_needs ?? 2000,
        ]);
    }

    // ── GET /api/foods/search ─────────────────────────────────
    public function searchFoods(Request $request)
    {
        $query   = $request->get('q', '');
        $user    = Auth::user();
        $allergens = $user->allergies->pluck('allergen')->toArray();

        $foods = Food::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->take(10)
            ->get()
            ->map(fn($f) => array_merge($this->formatFood($f), [
                'is_safe' => $this->checkSafe($f, $allergens),
            ]));

        return response()->json(['foods' => $foods]);
    }

    // ── GET /api/notifications/unread-count ──────────────────
    public function unreadCount()
    {
        return response()->json([
            'count' => Notification::where('user_id', Auth::id())
                ->where('is_read', false)->count(),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────
    private function formatFood(?Food $food): ?array
    {
        if (!$food) return null;
        return [
            'id'           => $food->id,
            'name'         => $food->name,
            'calories'     => $food->calories,
            'proteins'     => $food->proteins,
            'fat'          => $food->fat,
            'carbohydrate' => $food->carbohydrate,
            'origin'       => $food->origin,
            'meal_type'    => $food->meal_type,
            'composition'  => $food->composition,
        ];
    }

    private function checkSafe(Food $food, array $allergens): bool
    {
        $comp = strtolower($food->composition ?? '');
        foreach ($allergens as $a) {
            if (str_contains($comp, strtolower($a))) return false;
        }
        return true;
    }
}