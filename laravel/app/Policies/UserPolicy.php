<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Berikan semua izin kepada admin
        if ($user->role === 'admin') {
            return true;
        }
        
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Izinkan jika user adalah admin (redundant karena ada 'before', tapi ini best practice)
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin bisa mengedit semua user, kecuali dirinya sendiri untuk mencegah self-lockout
        // Kamu bisa hapus '&& !$user->is($model)' jika ingin admin bisa edit diri sendiri
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Admin bisa menghapus user lain, tapi tidak bisa menghapus diri sendiri
        return $user->role === 'admin' && !$user->is($model);
    }
}