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

    protected static ?string $navigationLabel = 'Nastavení notifikací';

    protected static ?string $title = 'Notifikace zpráv z formulářů';

    protected static ?int $navigationSort = 40;

    protected string $view = 'mipress-forms::filament.pages.form-notification-settings';

    public ?string $form_notification_preference = null;

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
            Section::make('Upozornění na nové zprávy')
                ->description('Zvolte, jak chcete dostávat upozornění na nově odeslané zprávy z formulářů.')
                ->schema([
                    Radio::make('form_notification_preference')
                        ->label('Způsob upozornění')
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

        Notification::make()
            ->title('Nastavení uloženo')
            ->success()
            ->send();
    }
}
