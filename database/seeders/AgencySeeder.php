<?php

namespace Database\Seeders;

use App\Models\Agency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $agencies = [
            [
                'name' => 'Dinas Kesehatan',
                'code' => 'DINKES',
                'description' => 'Dinas yang menangani masalah kesehatan masyarakat'
            ],
            [
                'name' => 'Dinas Pekerjaan Umum',
                'code' => 'DINPU',
                'description' => 'Dinas yang menangani infrastruktur dan pekerjaan umum'
            ],
            [
                'name' => 'Dinas Lingkungan Hidup',
                'code' => 'DLH',
                'description' => 'Dinas yang menangani masalah lingkungan hidup'
            ],
            [
                'name' => 'Dinas Perhubungan',
                'code' => 'DISHUB',
                'description' => 'Dinas yang menangani masalah transportasi dan perhubungan'
            ],
            [
                'name' => 'Dinas Pendidikan',
                'code' => 'DISDIK',
                'description' => 'Dinas yang menangani masalah pendidikan'
            ],
            
                [
                    'name' => 'Dinas Kehutanan',
                    'code' => 'DISHUT',
                    'description' => 'Dinas yang menangani masalah kehutanan dan konservasi alam'
                ],
                [
                    'name' => 'Dinas Sosial',
                    'code' => 'DINSOS',
                    'description' => 'Dinas yang menangani masalah kesejahteraan sosial'
                ],
                [
                    'name' => 'Dinas Komunikasi dan Informatika',
                    'code' => 'DISKOMINFO',
                    'description' => 'Dinas yang menangani masalah komunikasi dan informatika'
                ],
                [
                    'name' => 'Dinas Perumahan dan Pemukiman',
                    'code' => 'DISRUMKIM',
                    'description' => 'Dinas yang menangani masalah perumahan dan pemukiman'
                ],
                [
                    'name' => 'Dinas Kependudukan dan Pencatatan Sipil',
                    'code' => 'DISDUKCAPIL',
                    'description' => 'Dinas yang menangani masalah kependudukan dan pencatatan sipil'
                ],
                [
                    'name' => 'Dinas Perdagangan dan Perindustrian',
                    'code' => 'DISDAGPERIN',
                    'description' => 'Dinas yang menangani masalah perdagangan dan perindustrian'
                ],
                [
                    'name' => 'Dinas Penanaman Modal dan PTSP',
                    'code' => 'DPM-PTSP',
                    'description' => 'Dinas yang menangani penanaman modal dan pelayanan terpadu satu pintu'
                ],
                [
                    'name' => 'Dinas Pariwisata, Pemuda dan Olahraga',
                    'code' => 'DISPARPORA',
                    'description' => 'Dinas yang menangani masalah pariwisata, pemuda dan olahraga'
                ],
                [
                    'name' => 'Dinas Koperasi, Usaha Mikro, dan Tenaga Kerja',
                    'code' => 'DISKOP-UMK-TK',
                    'description' => 'Dinas yang menangani masalah koperasi, usaha mikro dan tenaga kerja'
                ],
                [
                    'name' => 'Dinas Ketahanan Pangan, Pertanian dan Perikanan',
                    'code' => 'DKP3',
                    'description' => 'Dinas yang menangani masalah ketahanan pangan, pertanian dan perikanan'
                ],
                [
                    'name' => 'Dinas Arsip dan Perpustakaan Daerah',
                    'code' => 'DISARPUSDA',
                    'description' => 'Dinas yang menangani masalah arsip dan perpustakaan daerah'
                ],
                [
                    'name' => 'Badan Pengelola Keuangan dan Aset Daerah',
                    'code' => 'BPKAD',
                    'description' => 'Badan yang mengelola keuangan dan aset daerah'
                ],
                [
                    'name' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia',
                    'code' => 'BKPSDM',
                    'description' => 'Badan yang menangani kepegawaian dan pengembangan SDM'
                ],
                [
                    'name' => 'Satuan Polisi Pamong Praja',
                    'code' => 'SATPOL-PP',
                    'description' => 'Satuan yang menangani ketertiban umum dan penegakan peraturan daerah'
                ]
        ];

        foreach ($agencies as $agency) {
            Agency::create($agency);
        }
    }
}
