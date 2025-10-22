<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_code',
        'name',
        'date_of_birth',
        'whatsapp_number',
        'address',
        'initial_diagnosis',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            do {
                $code = Str::upper(Str::random(6));
            } while (self::where('client_code', $code)->exists()); 

            $client->client_code = $code;
        });
    }
    public function sessions()
    {
    return $this->hasMany(ClientSession::class);
    }
}