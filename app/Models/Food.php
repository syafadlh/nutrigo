<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Food extends Model {
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'name','calories','proteins','fat','carbohydrate',
        'composition','origin','region','meal_type','image_url','is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    // Filter berdasarkan wilayah
    public function scopeByRegion($query, string $region) {
        return $query->where('origin', 'like', "%{$region}%")
                    ->orWhere('region', $region);
    }

    // Filter exclude alergen
    public function scopeExcludeAllergens($query, array $allergens) {
        foreach ($allergens as $allergen) {
            $query->where('composition', 'not like', "%{$allergen}%");
        }
        return $query;
    }

    public function scopeForMealType($query, string $type) {
        return $query->where('meal_type', $type);
    }

    public function histories() { return $this->hasMany(FoodHistory::class); }
}