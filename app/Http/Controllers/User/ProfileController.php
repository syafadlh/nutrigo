<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserAllergy;
use App\Models\UserMedicalNeed;
use App\Models\MealReminder;
use App\Services\CalorieCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller {

    public function __construct(private CalorieCalculatorService $calorieService) {}

    public function index() {
        $user         = Auth::user();
        $allergies    = $user->allergies;
        $medicalNeeds = $user->medicalNeeds()->where('is_active', true)->get();
        $reminders    = $user->reminders;
        $bmiCategory  = $this->calorieService->getBMICategory($user->bmi ?? 0);
        return view('user.profile', compact('user','allergies','medicalNeeds','reminders','bmiCategory'));
    }

    public function updateHealth(Request $request) {
        $request->validate([
            'height_cm'      => 'required|numeric|min:50|max:300',
            'weight_kg'      => 'required|numeric|min:10|max:500',
            'activity_level' => 'required|in:sedentary,light,moderate,active,very_active',
        ]);

        $user = Auth::user();
        $bmi  = $this->calorieService->calculateBMI($request->weight_kg, $request->height_cm);
        $bmr  = $this->calorieService->calculateBMR($request->weight_kg, $request->height_cm, $user->getAge(), $user->gender);
        $tdee = $this->calorieService->calculateTDEE($bmr, $request->activity_level);

        $user->update([
            'height_cm'           => $request->height_cm,
            'weight_kg'           => $request->weight_kg,
            'activity_level'      => $request->activity_level,
            'bmi'                 => $bmi,
            'daily_calorie_needs' => round($tdee),
        ]);

        return back()->with('success', 'Data kesehatan berhasil diperbarui!');
    }

    public function updateAllergies(Request $request) {
        $user = Auth::user();
        $user->allergies()->delete();

        $allergens = array_filter(array_merge(
            $request->get('allergens', []),
            $request->custom_allergy ? [$request->custom_allergy] : []
        ));

        foreach ($allergens as $a) {
            UserAllergy::create(['user_id' => $user->id, 'allergen' => $a]);
        }

        return back()->with('success', 'Daftar alergi diperbarui!');
    }

    public function updateReminders(Request $request) {
        $user = Auth::user();
        foreach (['breakfast','lunch','dinner'] as $meal) {
            $user->reminders()->where('meal_type', $meal)->updateOrCreate(
                ['user_id' => $user->id, 'meal_type' => $meal],
                ['reminder_time' => $request->get("{$meal}_time", '00:00'), 'is_active' => $request->boolean("{$meal}_active")]
            );
        }
        return back()->with('success', 'Pengingat makan diperbarui!');
    }

    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah!');
    }
}