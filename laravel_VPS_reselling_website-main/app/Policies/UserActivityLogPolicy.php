<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserActivityLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserActivityLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserActivityLog');
    }

    public function view(AuthUser $authUser, UserActivityLog $userActivityLog): bool
    {
        return $authUser->can('View:UserActivityLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserActivityLog');
    }

    public function update(AuthUser $authUser, UserActivityLog $userActivityLog): bool
    {
        return $authUser->can('Update:UserActivityLog');
    }

    public function delete(AuthUser $authUser, UserActivityLog $userActivityLog): bool
    {
        return $authUser->can('Delete:UserActivityLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UserActivityLog');
    }

    public function restore(AuthUser $authUser, UserActivityLog $userActivityLog): bool
    {
        return $authUser->can('Restore:UserActivityLog');
    }

    public function forceDelete(AuthUser $authUser, UserActivityLog $userActivityLog): bool
    {
        return $authUser->can('ForceDelete:UserActivityLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserActivityLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserActivityLog');
    }

    public function replicate(AuthUser $authUser, UserActivityLog $userActivityLog): bool
    {
        return $authUser->can('Replicate:UserActivityLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserActivityLog');
    }

}