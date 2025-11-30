<?php

namespace App\Services;

use App\Models\AgencyCategory;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    // Kategori utama HARDCODE - TIDAK PERLU DISIMPAN DI DATABASE
    const MAIN_CATEGORIES = [
        'Infrastruktur',
        'Lingkungan',
        'Pendidikan',
        'Kesehatan',
        'Transportasi',
        'Lainnya'
    ];

    /**
     * Dapatkan kategori utama (HARDCODE)
     */
    public function getMainCategories()
    {
        return self::MAIN_CATEGORIES;
    }

    /**
     * Buat sub category (SIMPLE)
     */
    public function createSubCategory($name, $mainCategory, $agencyId, $createdBy)
    {
        return AgencyCategory::create([
            'name' => $name,
            'main_category' => $mainCategory, // ⭐ SIMPAN MAIN CATEGORY DI KOLOM
            'agency_id' => $agencyId,
            'created_by' => $createdBy
        ]);
    }

    /**
     * Dapatkan sub kategori berdasarkan kategori utama
     */
    public function getSubCategories($mainCategory)
    {
        return AgencyCategory::where('main_category', $mainCategory)
            ->with('agency')
            ->orderBy('name')
            ->get();
    }

    /**
     * Cek apakah kategori utama memiliki sub kategori
     */
    public function hasSubCategories($mainCategory)
    {
        return AgencyCategory::where('main_category', $mainCategory)->exists();
    }
}
