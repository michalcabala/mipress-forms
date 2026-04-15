<?php

declare(strict_types=1);

namespace MiPress\Forms\Notifications;

use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use MiPress\Forms\Filament\Resources\FormSubmissionResource;
use MiPress\Forms\Models\FormSubmission;

class NewFormSubmission extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly FormSubmission $submission) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('mipress-forms::admin.notifications.new_submission.title'))
            ->body(__('mipress-forms::admin.notifications.new_submission.body', [
                'title' => $this->submission->form?->title ?? __('mipress-forms::admin.notifications.new_submission.untitled_form'),
                'id' => $this->submission->getKey(),
            ]))
            ->actions([
                Action::make('open')
                    ->label(__('mipress-forms::admin.notifications.new_submission.actions.open'))
                    ->url(FormSubmissionResource::getUrl('view', ['record' => $this->submission]))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
