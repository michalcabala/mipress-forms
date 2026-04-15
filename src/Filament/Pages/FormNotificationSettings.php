<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Pages;

use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use MiPress\Forms\Enums\FormNotificationPreference;
use MiPress\Forms\Filament\Clusters\FormsCluster;
use MiPress\Forms\Models\FormNotificationSetting;

class FormNotificationSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'fal-bell-ring';

    protected static ?string $cluster = FormsCluster::class;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static ?int $navigationSort = 40;

    protected string $view = 'mipress-forms::filament.pages.form-notification-settings';

    public ?string $form_notification_preference = null;

    public static function getNavigationLabel(): string
    {
        return __('mipress-forms::admin.pages.form_notification_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('mipress-forms::admin.pages.form_notification_settings.title');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('form_submission.view') === true;
    }

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->form_notification_preference = FormNotificationPreference::Both->value;

            return;
        }

        $setting = FormNotificationSetting::query()
            ->where('user_id', $user->getKey())
            ->first();

        $this->form_notification_preference = $setting?->preference instanceof FormNotificationPreference
            ? $setting->preference->value
            : FormNotificationPreference::Both->value;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('mipress-forms::admin.pages.form_notification_settings.section'))
                ->description(__('mipress-forms::admin.pages.form_notification_settings.description'))
                ->schema([
                    Radio::make('form_notification_preference')
                        ->label(__('mipress-forms::admin.pages.form_notification_settings.field'))
                        ->options(FormNotificationPreference::options())
                        ->required(),
                ]),
        ]);
    }

    public function save(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        FormNotificationSetting::query()->updateOrCreate(
            ['user_id' => $user->getKey()],
            ['preference' => $this->form_notification_preference ?? FormNotificationPreference::Both->value],
        );

        $selectedPreference = FormNotificationPreference::options()[$this->form_notification_preference ?? FormNotificationPreference::Both->value] ?? 'E-mail i administrace';

        Notification::make()
            ->title(__('mipress-forms::admin.pages.form_notification_settings.saved_title'))
            ->body(__('mipress-forms::admin.pages.form_notification_settings.saved_body', ['preference' => $selectedPreference]))
            ->success()
            ->send();
    }
}
