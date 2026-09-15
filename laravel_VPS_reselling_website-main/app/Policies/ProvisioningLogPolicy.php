<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProvisioningLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProvisioningLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProvisioningLog');
    }

    public function view(AuthUser $authUser, ProvisioningLog $provisioningLog): bool
    {
        return $authUser->can('View:ProvisioningLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProvisioningLog');
    }

    public function update(AuthUser $authUser, ProvisioningLog $provisioningLog): bool
    {
        return $authUser->can('Update:ProvisioningLog');
    }

    public function delete(AuthUser $authUser, ProvisioningLog $provisioningLog): bool
    {
        return $authUser->can('Delete:ProvisioningLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProvisioningLog');
    }

    public function restore(AuthUser $authUser, ProvisioningLog $provisioningLog): bool
    {
        return $authUser->can('Restore:ProvisioningLog');
    }

    public function forceDelete(AuthUser $authUser, ProvisioningLog $provisioningLog): bool
    {
        return $authUser->can('ForceDelete:ProvisioningLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProvisioningLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProvisioningLog');
    }

    public function replicate(AuthUser $authUser, ProvisioningLog $provisioningLog): bool
    {
        return $authUser->can('Replicate:ProvisioningLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProvisioningLog');
    }

}