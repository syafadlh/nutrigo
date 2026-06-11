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

        // Slot-based calorie targeting (requested by spec)
        $dailyCal = (float) ($user->daily_calorie_needs ?? 2000);
        $targets = [
            'breakfast' => $dailyCal * 0.30,
            'lunch'     => $dailyCal * 0.40,
            'dinner'    => $dailyCal * 0.30,
        ];

        $targetForMeal = $mealType && isset($targets[$mealType]) ? $targets[$mealType] : null;

        // Small tolerance is acceptable. Use +/- 25% to ensure feasibility.
        $tolerance = 0.25;
        $minCal = $targetForMeal !== null ? ($targetForMeal * (1 - $tolerance)) : null;
        $maxCalBySlot = $targetForMeal !== null ? ($targetForMeal * (1 + $tolerance)) : null;

        $foods = Food::where('is_active', true)
            ->when($province,  fn($q) => $q->where('origin', 'like', "%{$province}%"))
            // Slot-based selection: do NOT hard-filter by foods.meal_type.
            // We only use meal_type query param to compute slot calorie target.
            ->when($targetForMeal !== null, function ($q) use ($minCal, $maxCalBySlot) {
                $q->where('calories', '>=', $minCal)->where('calories', '<=', $maxCalBySlot);
            })
            ->when($maxCal && $targetForMeal === null, fn($q) => $q->where('calories', '<=', (float)$maxCal))
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

    public function detail(Food $food, Request $request)
    {
        // render partial modal HTML
        $html = view('user.partials.food_modal', ['food' => $food])->render();
        return response()->json(['success' => true, 'html' => $html]);
    }

    /**
     * Pilih menu sebagai rencana untuk meal_type tertentu (sarapan/siang/malam)
     * Akan menyimpan pada MenuRecommendation hari ini (create jika belum ada)
     */
    public function selectMenu(Request $request)
    {
        $request->validate([
            'food_id' => 'required|exists:foods,id',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        $menu = MenuRecommendation::firstOrCreate([
            'user_id' => $user->id,
            'recommendation_date' => $today,
        ], [
            'total_calories' => 0,
        ]);

        $field = $request->meal_type . '_id';
        $menu->{$field} = $request->food_id;
        $menu->is_saved = true;
        $menu->save();

        // Sync langsung ke history agar perubahan menu otomatis terlihat di dashboard & history.
        // (History sebelumnya hanya bertambah saat user menekan tombol confirm di dashboard.)
return response()->json([
    'success' => true,
    'message' => 'Menu berhasil disimpan.'
]);

        return response()->json(['success' => true, 'message' => 'Menu berhasil ditambahkan ke rencana dan dicatat ke riwayat.']);
    }

    /**
     * Konfirmasi log menu dari rencana (mengubah rencana menjadi catatan konsumsi)
     */
    public function confirmPlannedMenu(Request $request)
    {
        $request->validate([
            'meal_type' => 'required|in:breakfast,lunch,dinner',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        $menu = MenuRecommendation::where('user_id', $user->id)->where('recommendation_date', $today)->first();
        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Tidak ada menu terpilih untuk hari ini.'], 422);
        }

        $field = $request->meal_type . '_id';
        $foodId = $menu->{$field};
        if (!$foodId) {
            return response()->json(['success' => false, 'message' => 'Belum ada menu terpilih untuk waktu makan ini.'], 422);
        }

        $food = Food::find($foodId);
        if (!$food) {
            return response()->json(['success' => false, 'message' => 'Data makanan tidak ditemukan.'], 404);
        }

        // create food history
        FoodHistory::create([
            'user_id' => $user->id,
            'food_id' => $food->id,
            'meal_type' => $request->meal_type,
            'calories_consumed' => $food->calories,
            'consumed_date' => $today,
            'consumed_time' => now()->format('H:i:s'),
        ]);

        return response()->json(['success' => true, 'message' => 'Menu berhasil dicatat.']);
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