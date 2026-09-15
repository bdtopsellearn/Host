<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PackageAddon;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageAddonPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PackageAddon');
    }

    public function view(AuthUser $authUser, PackageAddon $packageAddon): bool
    {
        return $authUser->can('View:PackageAddon');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PackageAddon');
    }

    public function update(AuthUser $authUser, PackageAddon $packageAddon): bool
    {
        return $authUser->can('Update:PackageAddon');
    }

    public function delete(AuthUser $authUser, PackageAddon $packageAddon): bool
    {
        return $authUser->can('Delete:PackageAddon');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PackageAddon');
    }

    public function restore(AuthUser $authUser, PackageAddon $packageAddon): bool
    {
        return $authUser->can('Restore:PackageAddon');
    }

    public function forceDelete(AuthUser $authUser, PackageAddon $packageAddon): bool
    {
        return $authUser->can('ForceDelete:PackageAddon');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PackageAddon');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PackageAddon');
    }

    public function replicate(AuthUser $authUser, PackageAddon $packageAddon): bool
    {
        return $authUser->can('Replicate:PackageAddon');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PackageAddon');
    }

}