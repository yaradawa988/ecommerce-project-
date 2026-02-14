<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ActivityLog extends Model
{
   use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'ip',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /* ================== Relationships ================== */

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}