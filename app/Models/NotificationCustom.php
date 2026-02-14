<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class NotificationCustom extends Model
{
    use HasFactory;

    protected $table = 'notification_customs';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /* ================== Relationships ================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}