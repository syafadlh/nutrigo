<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('food_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_id')->constrained('foods')->onDelete('cascade');            $table->enum('meal_type', ['breakfast','lunch','dinner','snack']);
            $table->float('calories_consumed');
            $table->date('consumed_date');
            $table->time('consumed_time')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('food_histories'); }
};