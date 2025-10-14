<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'date_of_birth',
        'whatsapp_number',
        'address',
        'initial_diagnosis',
    ];

    public function sessions()
    {
    return $this->hasMany(ClientSession::class);
    }
}