<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Agency extends Model
{
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
