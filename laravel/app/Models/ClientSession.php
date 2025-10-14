<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'session_description',
        'session_date',
        'session_start_time',
        'session_end_time',
        'transfer_date',
        'payment_status',
        'payment_amount',
        'session_status',
        'medical_record_path',
    ];

    /**
     * Mendapatkan data klien yang memiliki sesi ini.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Mendapatkan data user (psikolog) yang memiliki sesi ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}