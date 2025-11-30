<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agency_id',

    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // public function setPasswordAttribute($value)
    // {
    //     if (!empty($value)) {
    //         $this->attributes['password'] = Hash::make($value);
    //     }
    // }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = $value ? Hash::make($value) : Hash::make('Password_1');
    // }
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'telegram_user_id', 'telegram_chat_id');
    }

    /**
     * Scope untuk user berdasarkan telegram_chat_id
     */
    public function scopeByTelegramChatId($query, $chatId)
    {
        return $query->where('telegram_chat_id', $chatId);
    }
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    // Scope untuk user admin_dinas
    public function scopeAdminDinas($query)
    {
        return $query->where('role', 'admin_dinas');
    }

    // Accessor untuk nama agency
    public function getAgencyNameAttribute()
    {
        return $this->agency ? $this->agency->name : 'Tidak ada dinas';
    }

    // Method untuk cek apakah user punya agency
    public function hasAgency()
    {
        return !is_null($this->agency_id);
    }
    
    //Cek apakah agency sudah memiliki admin
    
    public static function agencyHasAdmin($agencyId)
    {
        return static::where('agency_id', $agencyId)
            ->where('role', 'admin_dinas')
            ->exists();
    }

    
    //Dapatkan admin untuk agency tertentu
    
    public static function getAdminForAgency($agencyId)
    {
        return static::where('agency_id', $agencyId)
            ->where('role', 'admin_dinas')
            ->first();
    }
    public function isAdminDinas()
    {
        return $this->role === 'admin_dinas';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }
}
