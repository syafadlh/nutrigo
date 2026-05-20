<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MealReminder;
use App\Models\User;
use App\Models\UserAllergy;
use App\Models\UserMedicalNeed;
use App\Services\CalorieCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller {

    public function __construct(private CalorieCalculatorService $calorieService) {}

    public function showStep(int $step) {
        /** @var User $user */
$user = Auth::user();
        if ($user->onboarding_completed) return redirect()->route('user.dashboard');
        return view("onboarding.step{$step}", compact('user'));
    }

    // Step 1: Nickname + Tanggal Lahir + Jenis Kelamin
    public function saveStep1(Request $request) {
        $request->validate([
            'nickname'   => 'required|string|max:100',
            'birth_date' => 'required|date|before:today',
            'gender'     => 'required|in:male,female',
        ]);

        /** @var User $user */
$user = Auth::user();

$user->update([
            'nickname'          => $request->nickname,
            'birth_date'        => $request->birth_date,
            'gender'            => $request->gender,
            'onboarding_step'   => 2,
        ]);

        return redirect()->route('onboarding.step', ['step' => 2]);
    }

    // Step 2: Info aplikasi + Wilayah
    public function saveStep2(Request $request) {
        $request->validate([
            'province' => 'required|string',
            'city'     => 'required|string|max:100',
        ]);

        /** @var User $user */
$user = Auth::user();

$user->update([
            'province'        => $request->province,
            'city'            => $request->city,
            'onboarding_step' => 3,
        ]);

        return redirect()->route('onboarding.step', ['step' => 3]);
    }

    // Step 3: Alergi
    public function saveStep3(Request $request) {
        /** @var User $user */
$user = Auth::user();
        $user->allergies()->delete();

        if ($request->has('allergens') && is_array($request->allergens)) {
            foreach ($request->allergens as $allergen) {
                UserAllergy::create(['user_id' => $user->id, 'allergen' => $allergen]);
            }
        }

        if ($request->custom_allergy) {
            UserAllergy::create(['user_id' => $user->id, 'allergen' => $request->custom_allergy]);
        }

        $user->update(['onboarding_step' => 4]);
        return redirect()->route('onboarding.step', ['step' => 4]);
    }

    // Step 4: Kebutuhan medis khusus
    public function saveStep4(Request $request) {
        /** @var User $user */
$user = Auth::user();

        if ($request->has_medical_need === 'yes') {
            $request->validate([
                'food_item'     => 'required|string',
                'quantity'      => 'required|integer|min:1',
                'unit'          => 'required|string',
                'duration_type' => 'required|in:daily,weekly,yearly,forever',
                'start_date'    => 'nullable|date',
                'end_date'      => 'nullable|date|after:start_date',
            ]);

            UserMedicalNeed::create([
                'user_id'       => $user->id,
                'food_item'     => $request->food_item,
                'quantity'      => $request->quantity,
                'unit'          => $request->unit,
                'duration_type' => $request->duration_type,
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date,
            ]);
        }

        $user->update(['onboarding_step' => 5]);
        return redirect()->route('onboarding.step', ['step' => 5]);
    }

    // Step 5: TB, BB → Hitung BMI & Kalori
    public function saveStep5(Request $request) {
        $request->validate([
            'height_cm'      => 'required|numeric|min:50|max:300',
            'weight_kg'      => 'required|numeric|min:10|max:500',
            'activity_level' => 'required|in:sedentary,light,moderate,active,very_active',
        ]);

        /** @var User $user */
$user = Auth::user();
        $age  = $user->getAge();

        $bmi  = $this->calorieService->calculateBMI($request->weight_kg, $request->height_cm);
        $bmr  = $this->calorieService->calculateBMR($request->weight_kg, $request->height_cm, $age, $user->gender);
        $tdee = $this->calorieService->calculateTDEE($bmr, $request->activity_level);

        $user->update([
            'height_cm'           => $request->height_cm,
            'weight_kg'           => $request->weight_kg,
            'activity_level'      => $request->activity_level,
            'bmi'                 => $bmi,
            'daily_calorie_needs' => round($tdee),
            'onboarding_completed'=> true,
            'onboarding_step'     => 5,
        ]);

        // Buat reminder default
        MealReminder::insert([
            ['user_id'=>$user->id,'meal_type'=>'breakfast','reminder_time'=>'07:00:00','is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$user->id,'meal_type'=>'lunch','reminder_time'=>'12:00:00','is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$user->id,'meal_type'=>'dinner','reminder_time'=>'18:30:00','is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
        ]);

        return redirect()->route('user.dashboard')->with('welcome', true);
    }
}