<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgencyCategory;
use App\Services\CategoryService;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoryService = app(CategoryService::class);

        // Buat main categories di database (untuk hierarki)
        $mainCategories = [
            'Infrastruktur',
            'Lingkungan',
            'Pendidikan',
            'Kesehatan',
            'Transportasi',
            'Lainnya'
        ];

        foreach ($mainCategories as $category) {
            $categoryService->createMainCategory($category, 1);
        }

        // Sub kategori untuk Dinas Kehutanan (agency_id = 6)
        $kehutananSubCategories = [
            'Infrastruktur' => ['Jalan Hutan', 'Jembatan Kayu', 'Pos Jagawana'],
            'Lingkungan' => ['Reboisasi', 'Kebakaran Hutan', 'Satwa Liar', 'Izin Penebangan'],
        ];

        foreach ($kehutananSubCategories as $mainCat => $subCats) {
            foreach ($subCats as $subCat) {
                $categoryService->createSubCategory($subCat, $mainCat, 6, 1); // agency_id = 6 (Dinas Kehutanan)
            }
        }

        // Sub kategori untuk Dinas Kesehatan (agency_id = 1)
        $kesehatanSubCategories = [
            'Kesehatan' => ['Rumah Sakit', 'Puskesmas', 'Obat', 'Pelayanan Kesehatan'],
            'Infrastruktur' => ['Bangunan Rumah Sakit', 'Fasilitas Kesehatan'],
        ];

        foreach ($kesehatanSubCategories as $mainCat => $subCats) {
            foreach ($subCats as $subCat) {
                $categoryService->createSubCategory($subCat, $mainCat, 1, 1); // agency_id = 1 (Dinas Kesehatan)
            }
        }
    }
}
