<?php
namespace App\Console\Commands;

use App\Models\User;
use App\Services\MenuRecommendationService;
use Illuminate\Console\Command;

class GenerateDailyMenus extends Command
{
    protected $signature   = 'nutrigo:generate-menus';
    protected $description = 'Generate rekomendasi menu harian untuk semua user aktif';

    public function __construct(private MenuRecommendationService $menuService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $users = User::where('is_admin', false)
            ->where('onboarding_completed', true)
            ->get();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $generated = 0;
        foreach ($users as $user) {
            try {
                $this->menuService->generateDailyMenu($user);
                $generated++;
            } catch (\Exception $e) {
                $this->warn("Gagal generate menu untuk user #{$user->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$generated} menu harian berhasil di-generate!");
    }
}