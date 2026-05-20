<?php
namespace App\Console\Commands;

use App\Models\Food;
use Illuminate\Console\Command;

class ImportAllDatasets extends Command
{
    protected $signature   = 'nutrigo:import-all';
    protected $description = 'Import semua dataset CSV ke database';

    public function handle(): void
    {
        $this->info('🚀 Mulai import semua dataset NutriGo...');
        $this->newLine();

        // 1. Import makanan utama
        $this->importFoodFinal();

        // 2. Tambahkan origin dari dataset kedua
        $this->importOrigins();

        // 3. Tampilkan ringkasan
        $this->newLine();
        $this->table(
            ['Dataset', 'Status', 'Jumlah'],
            [
                ['db_food_final.csv',      '✅ Imported', Food::count().' makanan'],
                ['db_food_with_origin.csv','✅ Origin updated', Food::whereNotNull('origin')->count().' punya origin'],
                ['bodyfat_cleaned.csv',    'ℹ️ Tidak diimport', 'Rumus BMI dihitung di PHP langsung'],
                ['train_data.csv',         'ℹ️ Tidak diimport', 'Logika rekomendasi ada di PHP service'],
            ]
        );

        $this->newLine();
        $this->info('✅ Import selesai! Sistem siap digunakan.');
    }

    private function importFoodFinal(): void
    {
        $this->info('📂 Import db_food_final.csv...');
        $path = database_path('data/db_food_final.csv');

        if (!file_exists($path)) {
            $this->error("File tidak ada: {$path}");
            $this->line("Salin file CSV ke: database/data/db_food_final.csv");
            return;
        }

        $handle  = fopen($path, 'r');
        $headers = fgetcsv($handle, 0, ';');
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $bar = $this->output->createProgressBar();
        $bar->start();

        $count = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < 3) continue;
            $data = array_combine($headers, array_pad(array_map('trim', $row), count($headers), ''));
            $name = $data['name'] ?? '';
            if (empty($name)) continue;

            $mealType = $this->detectMealType($name, $data['composition'] ?? '', (float)($data['calories'] ?? 0));

            Food::updateOrCreate(
                ['name' => $name],
                [
                    'calories'     => (float)($data['calories'] ?? 0),
                    'proteins'     => (float)($data['proteins'] ?? 0),
                    'fat'          => (float)($data['fat'] ?? 0),
                    'carbohydrate' => (float)($data['carbohydrate'] ?? 0),
                    'composition'  => $data['composition'] ?? null,
                    'meal_type'    => $mealType,
                    'is_active'    => true,
                ]
            );
            $count++;
            $bar->advance();
        }

        fclose($handle);
        $bar->finish();
        $this->newLine();
        $this->info("   → {$count} makanan berhasil diimport");
    }

    private function importOrigins(): void
    {
        $this->info('📂 Update origin dari db_food_with_origin.csv...');
        $path = database_path('data/db_food_with_origin.csv');

        if (!file_exists($path)) {
            $this->warn("File tidak ada: {$path} — skip");
            return;
        }

        $handle  = fopen($path, 'r');
        $headers = fgetcsv($handle, 0, ';');
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $count = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < 4) continue;
            $data   = array_combine($headers, array_pad(array_map('trim', $row), count($headers), ''));
            $name   = $data['name'] ?? '';
            $origin = $data['origin'] ?? '';

            if (empty($name) || empty($origin)) continue;

            // Coba exact match dulu, lalu like
            $updated = Food::where('name', $name)->update(['origin' => $origin]);
            if (!$updated) {
                Food::where('name', 'like', "%{$name}%")->update(['origin' => $origin]);
            }
            $count++;
        }

        fclose($handle);
        $this->info("   → {$count} origin diperbarui");
    }

    private function detectMealType(string $name, string $composition, float $calories): string
    {
        $n = strtolower($name);
        $c = strtolower($composition);

        $breakfast = ['roti','bubur','oatmeal','sereal','lontong','ketupat','nasi uduk'];
        foreach ($breakfast as $w) { if (str_contains($n, $w)) return 'breakfast'; }

        $snack = ['keripik','biskuit','kue','agar','jelly','permen','jus','teh ','kopi ','susu','minuman'];
        foreach ($snack as $w) { if (str_contains($n, $w) || str_contains($c, $w)) return 'snack'; }
        if ($calories > 0 && $calories < 80) return 'snack';

        $dinner = ['sup ','soto','rawon','gulai','rendang','opor','semur','pindang'];
        foreach ($dinner as $w) { if (str_contains($n, $w)) return 'dinner'; }

        return 'lunch';
    }
}