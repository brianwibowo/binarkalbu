<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'recap_date',
        'session_count',
        'new_chats',
        'new_client_goals',
        'gmap_reviews',
        'source_tiktok',
        'source_google',
        'source_instagram',
        'source_friend',
        'jam_gandeng',
        'extra_notes',
    ];
}