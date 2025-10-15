<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasAvatar; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements HasAvatar
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    // 4. TAMBAHKAN FUNGSI BARU INI
    public function getFilamentAvatarUrl(): ?string
    {
        // Jika ada path foto profil dan file-nya ada di storage
        if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
            // Kembalikan URL publik ke file tersebut
            return Storage::url($this->profile_photo_path); 
        }
        
        // Jika tidak ada, kembalikan null (Filament akan menampilkan inisial nama)
        return null;
    }
}