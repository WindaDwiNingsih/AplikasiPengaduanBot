<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'user_id',
        'old_status',
        'new_status',
        'notes',
        'created_at',
        'updated_at'
    ];

    // Cast kolom timestamp ke Carbon agar mudah diolah
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi: Setiap histori dibuat oleh satu User (Agen/Admin)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Setiap histori milik satu Complaint
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
