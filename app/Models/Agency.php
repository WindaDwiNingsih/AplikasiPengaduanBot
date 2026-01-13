<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code', 
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasAdmin()
    {
        return $this->users()
            ->where('role', 'admin_dinas')
            ->exists();
    }
}
