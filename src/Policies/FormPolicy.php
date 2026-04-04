<?php

declare(strict_types=1);

namespace MiPress\Forms\Policies;

use App\Models\User;
use MiPress\Forms\Models\Form;

class FormPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('form.view');
    }

    public function view(User $user, Form $form): bool
    {
        return $user->hasPermissionTo('form.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('form.create');
    }

    public function update(User $user, Form $form): bool
    {
        return $user->hasPermissionTo('form.update');
    }

    public function delete(User $user, Form $form): bool
    {
        return $user->hasPermissionTo('form.delete');
    }

    public function restore(User $user, Form $form): bool
    {
        return $user->hasPermissionTo('form.delete');
    }

    public function forceDelete(User $user, Form $form): bool
    {
        return $user->hasPermissionTo('form.delete');
    }
}
