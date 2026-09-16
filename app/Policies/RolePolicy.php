<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

/**
 * Guards Filament Shield's Roles resource. Shield's own generated permission
 * matrix (view_role, create_role, etc.) was never run/seeded, so rather than
 * gate on ->can() against permissions that don't exist (which Filament's
 * "no policy = allowed" fallback would silently treat as unrestricted), this
 * checks the super_admin role directly: only Super Admin manages roles.
 */
class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Role $role): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return $user->hasRole('super_admin');
    }
}
