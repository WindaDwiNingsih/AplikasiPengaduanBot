<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected $casts = [
        'submitted_at' => 'datetime', // ⭐ Pastikan kolom ini ada
        // Jika Anda memiliki kolom 'resolved_at' dari migrasi sebelumnya, tambahkan juga:
        // 'resolved_at' => 'datetime',
    ];
}
