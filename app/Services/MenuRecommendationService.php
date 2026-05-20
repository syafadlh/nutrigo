<?php
namespace App\Services;

use App\Models\Food;
use App\Models\User;
use App\Models\MenuRecommendation;
use App\Models\FoodHistory;
use Carbon\Carbon;

class MenuRecommendationService
{
    public function __construct(private CalorieCalculatorService $calorieService) {}

    /**
     * Generate rekomendasi menu harian untuk user
     * Menggunakan Content-Based Filtering dari train_data
     */
    public function generateDailyMenu(User $user): MenuRecommendation
    {
        $today = Carbon::today();

        // Cek jika sudah ada hari ini
        $existing = MenuRecommendation::where('user_id', $user->id)
            ->where('recommendation_date', $today)
            ->first();
        if ($existing) return $existing;

        // Hitung target kalori per waktu makan
        $dailyCal  = $user->daily_calorie_needs ?? 2000;
        $targets   = [
            'breakfast' => $dailyCal * 0.30,
            'lunch'     => $dailyCal * 0.40,
            'dinner'    => $dailyCal * 0.30,
        ];

        // Ambil alergen user
        $allergens = $user->allergies->pluck('allergen')->toArray();

        // Ambil makanan yang sudah dimakan 3 hari terakhir (untuk variasi)
        $recentIds = FoodHistory::where('user_id', $user->id)
            ->where('consumed_date', '>=', Carbon::now()->subDays(3))
            ->pluck('food_id')
            ->toArray();

        // Pilih menu berdasarkan profil user (Content-Based dari train_data)
        $breakfast = $this->pickFoodByProfile($user, 'breakfast', $targets['breakfast'], $allergens, $recentIds);
        $lunch     = $this->pickFoodByProfile($user, 'lunch',     $targets['lunch'],     $allergens, array_merge($recentIds, [$breakfast?->id]));
        $dinner    = $this->pickFoodByProfile($user, 'dinner',    $targets['dinner'],    $allergens, array_merge($recentIds, [$breakfast?->id, $lunch?->id]));

        $total = ($breakfast?->calories ?? 0) + ($lunch?->calories ?? 0) + ($dinner?->calories ?? 0);

        return MenuRecommendation::create([
            'user_id'             => $user->id,
            'recommendation_date' => $today,
            'breakfast_id'        => $breakfast?->id,
            'lunch_id'            => $lunch?->id,
            'dinner_id'           => $dinner?->id,
            'total_calories'      => $total,
        ]);
    }

    /**
     * INTI SISTEM REKOMENDASI:
     * Pilih makanan berdasarkan profil user (BMI, gender, usia)
     * + filter alergi + filter wilayah + variasi (anti-repeat)
     *
     * Logika ini terinspirasi dari train_data.csv yang
     * menggabungkan profil user (Sex, Age, Height, Weight, BMI)
     * dengan data makanan (calories, proteins, fat, carbohydrate)
     */
    private function pickFoodByProfile(
        User   $user,
        string $mealType,
        float  $targetCalories,
        array  $allergens,
        array  $excludeIds
    ): ?Food {

        // ── 1. Tentukan range kalori berdasarkan profil ────────────
        $bmi    = $user->bmi ?? 22;
        $gender = $user->gender ?? 'male';
        $age    = $user->getAge();

        // Toleransi kalori per porsi (berdasarkan pola di train_data)
        // BMI tinggi → makanan lebih rendah kalori
        // BMI rendah → makanan lebih tinggi kalori
        $tolerance = match(true) {
            $bmi < 18.5 => 0.40, // underweight: rentang lebar, terima makanan lebih kalori
            $bmi < 25.0 => 0.30, // normal: rentang standar
            $bmi < 30.0 => 0.25, // overweight: lebih ketat, prioritas rendah lemak
            default     => 0.20, // obese: sangat ketat
        };

        $minCal = $targetCalories * (1 - $tolerance);
        $maxCal = $targetCalories * (1 + $tolerance);

        // ── 2. Bangun query dasar ─────────────────────────────────
        $query = Food::where('is_active', true)
            ->where('meal_type', $mealType)
            ->where('calories', '>=', $minCal)
            ->where('calories', '<=', $maxCal)
            ->whereNotIn('id', array_filter($excludeIds));

        // ── 3. Filter alergen ──────────────────────────────────────
        foreach ($allergens as $allergen) {
            $query->where('composition', 'not like', "%{$allergen}%");
        }

        // ── 4. Preferensi berdasarkan BMI ─────────────────────────
        if ($bmi >= 25) {
            // Overweight/obese: prioritaskan makanan rendah lemak
            $query->orderBy('fat', 'asc');
        } elseif ($bmi < 18.5) {
            // Underweight: prioritaskan makanan tinggi protein
            $query->orderBy('proteins', 'desc');
        }

        // ── 5. Prioritas wilayah lokal ────────────────────────────
        $foods = $query->get();

        if ($foods->isEmpty()) {
            // Fallback: cari tanpa filter range kalori
            $foods = Food::where('is_active', true)
                ->where('meal_type', $mealType)
                ->whereNotIn('id', array_filter($excludeIds))
                ->get();

            // Filter alergen manual
            foreach ($allergens as $allergen) {
                $foods = $foods->filter(fn($f) => !str_contains(strtolower($f->composition ?? ''), strtolower($allergen)));
            }
        }

        if ($foods->isEmpty()) return null;

        // ── 6. Scoring sistem: prioritaskan makanan lokal ─────────
        $province = $user->province ?? '';
        $scored   = $foods->map(function (Food $food) use ($province, $targetCalories, $bmi) {
            $score = 0;

            // +30 poin jika dari wilayah user
            if ($province && str_contains(strtolower($food->origin ?? ''), strtolower($province))) {
                $score += 30;
            }

            // +20 poin jika ada origin (makanan nusantara teridentifikasi)
            if ($food->origin) $score += 20;

            // +10 poin jika kalori mendekati target (makin dekat makin tinggi)
            $calorieDiff = abs($food->calories - $targetCalories);
            $score += max(0, 10 - ($calorieDiff / 50));

            // +10 poin untuk makanan tinggi protein jika BMI rendah
            if ($bmi < 18.5 && $food->proteins > 15) $score += 10;

            // +10 poin untuk makanan rendah lemak jika BMI tinggi
            if ($bmi >= 25 && $food->fat < 10) $score += 10;

            return ['food' => $food, 'score' => $score];
        });

        // Ambil top 5, lalu pilih random dari top 5 (agar ada variasi)
        $top5 = $scored->sortByDesc('score')->take(5)->pluck('food');
        return $top5->random();
    }

    /**
     * Regenerate menu (hapus menu hari ini, buat baru)
     */
    public function regenerateMenu(User $user): MenuRecommendation
    {
        MenuRecommendation::where('user_id', $user->id)
            ->where('recommendation_date', Carbon::today())
            ->delete();
        return $this->generateDailyMenu($user);
    }

    /**
     * Cek apakah makanan cocok untuk user (skor kompatibilitas)
     */
    public function getFoodCompatibilityScore(Food $food, User $user): array
    {
        $allergens  = $user->allergies->pluck('allergen')->toArray();
        $bmi        = $user->bmi ?? 22;
        $issues     = [];
        $score      = 100;

        // Cek alergen
        foreach ($allergens as $a) {
            if (str_contains(strtolower($food->composition ?? ''), strtolower($a))) {
                $issues[] = "⚠️ Mengandung {$a} (alergenmu)";
                $score   -= 50;
            }
        }

        // Cek lemak untuk overweight
        if ($bmi >= 25 && $food->fat > 20) {
            $issues[] = "⚠️ Lemak cukup tinggi ({$food->fat}g) untuk BMI-mu";
            $score   -= 15;
        }

        // Cek kalori
        $dailyCal = $user->daily_calorie_needs ?? 2000;
        if ($food->calories > $dailyCal * 0.5) {
            $issues[] = "⚠️ Kalori sangat tinggi untuk 1 porsi";
            $score   -= 10;
        }

        return [
            'score'     => max(0, $score),
            'issues'    => $issues,
            'is_safe'   => empty(array_filter($issues, fn($i) => str_contains($i, 'alergen'))),
            'grade'     => match(true) {
                $score >= 90 => '✅ Sangat cocok',
                $score >= 70 => '👍 Cocok',
                $score >= 50 => '⚠️ Cukup cocok',
                default      => '❌ Kurang cocok',
            },
        ];
    }
}