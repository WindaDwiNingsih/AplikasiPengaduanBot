<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'main_category', // ⭐ CUKUP INI SAJA
        'agency_id',
        'created_by'
    ];

    // Relasi tetap sama
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope untuk main categories
    public function scopeMainCategories($query)
    {
        // Ambil unique main_category dari database
        return $query->whereNotNull('main_category')
            ->distinct('main_category')
            ->pluck('main_category');
    }

    // Scope untuk sub categories by main category
    public function scopeSubCategoriesOf($query, $mainCategory)
    {
        return $query->where('main_category', $mainCategory)
            ->orderBy('name');
    }
}
