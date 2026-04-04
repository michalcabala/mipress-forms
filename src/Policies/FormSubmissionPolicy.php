<?php

declare(strict_types=1);

namespace MiPress\Forms\Policies;

use App\Models\User;
use MiPress\Forms\Models\FormSubmission;

class FormSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('form_submission.view');
    }

    public function view(User $user, FormSubmission $formSubmission): bool
    {
        return $user->hasPermissionTo('form_submission.view');
    }

    public function update(User $user, FormSubmission $formSubmission): bool
    {
        return $user->hasPermissionTo('form_submission.update');
    }

    public function delete(User $user, FormSubmission $formSubmission): bool
    {
        return $user->hasPermissionTo('form_submission.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('form_submission.delete');
    }
}
