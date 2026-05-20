<?php
namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        // ── Step 1: Import db_food_final.csv (1345 makanan) ───────
        $this->importFoodFinal();

        // ── Step 2: Update kolom origin dari db_food_with_origin.csv
        $this->importOrigins();

        $this->command->info('✅ Total makanan: ' . Food::count());
    }

    private function importFoodFinal(): void
    {
        $path = database_path('data/db_food_final.csv');
        if (!file_exists($path)) {
            $this->command->error("❌ File tidak ditemukan: {$path}");
            return;
        }

        $handle  = fopen($path, 'r');
        $headers = fgetcsv($handle, 0, ';'); // separator titik koma
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $count = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < count($headers)) continue;
            $data = array_combine($headers, array_map('trim', $row));

            $name = $data['name'] ?? '';
            if (empty($name)) continue;

            Food::updateOrCreate(
                ['name' => $name],
                [
                    'calories'     => (float)($data['calories'] ?? 0),
                    'proteins'     => (float)($data['proteins'] ?? 0),
                    'fat'          => (float)($data['fat'] ?? 0),
                    'carbohydrate' => (float)($data['carbohydrate'] ?? 0),
                    'composition'  => $data['composition'] ?? null,
                    'origin'       => null,
                    'meal_type'    => $this->detectMealType($data),
                    'is_active'    => true,
                ]
            );
            $count++;
        }
        fclose($handle);
        $this->command->info("✅ Import db_food_final: {$count} makanan");
    }

    private function importOrigins(): void
    {
        $path = database_path('data/db_food_with_origin.csv');
        if (!file_exists($path)) {
            $this->command->warn("⚠️ db_food_with_origin.csv tidak ditemukan, skip.");
            return;
        }

        $handle  = fopen($path, 'r');
        $headers = fgetcsv($handle, 0, ';');
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $count = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < count($headers)) continue;
            $data   = array_combine($headers, array_map('trim', $row));
            $name   = $data['name'] ?? '';
            $origin = $data['origin'] ?? null;

            if (empty($name) || empty($origin)) continue;

            // Update origin ke makanan yang namanya cocok
            Food::where('name', 'like', "%{$name}%")
                ->whereNull('origin')
                ->update(['origin' => $origin]);
            $count++;
        }
        fclose($handle);
        $this->command->info("✅ Update origin: {$count} makanan diperbarui");
    }

    private function detectMealType(array $data): string
    {
        $name        = strtolower(trim($data['name'] ?? ''));
        $composition = strtolower(trim($data['composition'] ?? ''));
        $calories    = (float)($data['calories'] ?? 0);

        // Sarapan: makanan ringan < 300 kalori atau nama mengandung kata sarapan
        $breakfastWords = ['roti','bubur','oatmeal','sereal','pancake','nasi uduk','ketupat','lontong'];
        foreach ($breakfastWords as $w) {
            if (str_contains($name, $w)) return 'breakfast';
        }

        // Snack: kalori sangat rendah < 100 atau kata snack
        $snackWords = ['keripik','biskuit','kue','agar','jelly','permen','minuman','jus','teh','kopi','susu'];
        foreach ($snackWords as $w) {
            if (str_contains($name, $w) || str_contains($composition, $w)) return 'snack';
        }
        if ($calories > 0 && $calories < 100) return 'snack';

        // Makan malam: makanan berat
        $dinnerWords = ['sup','soto','rawon','gulai','rendang','opor','semur'];
        foreach ($dinnerWords as $w) {
            if (str_contains($name, $w)) return 'dinner';
        }

        // Default lunch
        return 'lunch';
    }
}