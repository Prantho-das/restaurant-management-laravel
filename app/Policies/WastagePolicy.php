<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Wastage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WastagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Wastage');
    }

    public function view(AuthUser $authUser, Wastage $wastage): bool
    {
        return $authUser->can('View:Wastage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Wastage');
    }

    public function update(AuthUser $authUser, Wastage $wastage): bool
    {
        return $authUser->can('Update:Wastage');
    }

    public function delete(AuthUser $authUser, Wastage $wastage): bool
    {
        return $authUser->can('Delete:Wastage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Wastage');
    }

    public function restore(AuthUser $authUser, Wastage $wastage): bool
    {
        return $authUser->can('Restore:Wastage');
    }

    public function forceDelete(AuthUser $authUser, Wastage $wastage): bool
    {
        return $authUser->can('ForceDelete:Wastage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Wastage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Wastage');
    }

    public function replicate(AuthUser $authUser, Wastage $wastage): bool
    {
        return $authUser->can('Replicate:Wastage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Wastage');
    }
}
