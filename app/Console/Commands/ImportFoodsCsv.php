<?php
namespace App\Console\Commands;

use App\Models\Food;
use Illuminate\Console\Command;

class ImportFoodsCsv extends Command
{
    protected $signature   = 'nutrigo:import-foods {file? : Path ke file CSV}';
    protected $description = 'Import data makanan dari file CSV';

    public function handle(): void
    {
        $filePath = $this->argument('file') ?? database_path('data/db_food_final.csv');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            $this->line("Contoh: php artisan nutrigo:import-foods database/data/db_food_final.csv");
            return;
        }

        $handle  = fopen($filePath, 'r');
        $headers = fgetcsv($handle, 0, ';');

        if (!$headers) {
            $this->error('File CSV kosong atau tidak valid.');
            return;
        }

        // Normalize header keys
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $this->info("📂 Membaca file: {$filePath}");
        $this->info("📋 Kolom ditemukan: " . implode(', ', $headers));

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $data = array_combine($headers, array_map('trim', $row));

            $name = $data['name'] ?? '';
            if (empty($name)) { $skipped++; continue; }

            $mealType = $this->detectMealType(strtolower($name), $data['meal_type'] ?? '');

            Food::updateOrCreate(
                ['name' => $name],
                [
                    'calories'     => (float)($data['calories'] ?? 0),
                    'proteins'     => (float)($data['proteins'] ?? 0),
                    'fat'          => (float)($data['fat'] ?? 0),
                    'carbohydrate' => (float)($data['carbohydrate'] ?? 0),
                    'composition'  => $data['composition'] ?? null,
                    'origin'       => $data['origin'] ?? null,
                    'meal_type'    => $mealType,
                    'is_active'    => true,
                ]
            );
            $imported++;
        }

        fclose($handle);

        $this->info("✅ Import selesai!");
        $this->table(['Hasil', 'Jumlah'], [
            ['Berhasil diimport', $imported],
            ['Dilewati (kosong)', $skipped],
        ]);
    }

    private function detectMealType(string $name, string $existing = ''): string
    {
        if (!empty($existing) && in_array($existing, ['breakfast','lunch','dinner','snack'])) {
            return $existing;
        }

        $breakfast = ['roti','bubur','oatmeal','sereal','pancake','toast','cornflake'];
        $snack     = ['snack','keripik','biskuit','kue','jus','smoothie','minuman'];

        foreach ($breakfast as $kw) {
            if (str_contains($name, $kw)) return 'breakfast';
        }
        foreach ($snack as $kw) {
            if (str_contains($name, $kw)) return 'snack';
        }

        return 'lunch';
    }
}