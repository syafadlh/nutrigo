<?php
namespace App\Console\Commands;

use App\Models\MealReminder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMealReminders extends Command
{
    protected $signature   = 'nutrigo:send-reminders';
    protected $description = 'Kirim notifikasi pengingat makan sesuai jadwal user';

    public function handle(): void
    {
        $now       = Carbon::now()->format('H:i');
        $reminders = MealReminder::where('is_active', true)
            ->whereRaw("TIME_FORMAT(reminder_time, '%H:%i') = ?", [$now])
            ->with('user')
            ->get();

        $mealLabels = [
            'breakfast' => 'Sarapan',
            'lunch'     => 'Makan Siang',
            'dinner'    => 'Makan Malam',
        ];

        $sent = 0;
foreach ($reminders as $reminder) {

    $label = $mealLabels[$reminder->meal_type]
        ?? ucfirst($reminder->meal_type);

    $userName = $reminder->user->nickname
        ?? $reminder->user->name;

    Notification::create([
        'user_id' => $reminder->user_id,
        'title'   => "⏰ Waktunya {$label}!",
        'message' => "Hei {$userName}! Jangan lupa {$label} ya. Cek menu rekomendasimu hari ini 🍽️",
        'type'    => 'reminder',
        'is_read' => false,
    ]);

    $sent++;
}

        $this->info("✅ {$sent} notifikasi reminder berhasil dikirim pada {$now}");
    }
}