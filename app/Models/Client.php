<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_code',
        'name',
        'date_of_birth',
        'whatsapp_number',
        'address',
        'initial_diagnosis',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(ClientSession::class);
    }
}