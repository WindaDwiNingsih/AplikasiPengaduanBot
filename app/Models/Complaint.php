<?php
// app/Models/Complaint.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'agency_id',
        'telegram_user_id',
        'telegram_username',
        'title',
        'category',
        'description',
        'location',
        'photos',
        'status'
    ];

    protected $casts = [
        'photos' => 'array',
        'location' => 'json',
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];
    public function histories()
    {
        // Pastikan nama class yang dipanggil di sini sudah benar
        return $this->hasMany(ComplaintHistory::class)->latest();
    }
    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
    // Scope untuk filter by agency
    public function scopeByAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }
    /**
     * Scope untuk filter status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter kategori
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // /**
    //  * Scope untuk filter tanggal
    //  */
    // public function scopeDateRange($query, $startDate, $endDate)
    // {
    //     return $query->whereBetween('created_at', [$startDate, $endDate]);
    // }

    // /**
    //  * Get user name (accessor)
    //  */
    // public function getUserNameAttribute()
    // {
    //     return $this->user ? $this->user->name : 'Unknown User';
    // }
    
}
