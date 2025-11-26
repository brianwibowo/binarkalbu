<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsCollection;

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

    protected $casts = [
        'medical_record_path' => 'array',
        'session_date' => 'date',
        'transfer_date' => 'date',
    ];

    /**
     * Get medical record files as array.
     * Ensure always return array, even if null or single string
     */
    public function getMedicalRecordsAttribute(): array
    {
        $value = $this->medical_record_path;
        if (empty($value)) return [];
        if (is_array($value)) return array_filter($value, fn($f) => !empty($f));
        return [$value];
    }

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