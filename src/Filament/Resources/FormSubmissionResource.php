<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Resources;

use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use MiPress\Core\Enums\UserRole;
use MiPress\Forms\Filament\Clusters\FormsCluster;
use MiPress\Forms\Filament\Resources\FormSubmissionResource\Pages\ListFormSubmissions;
use MiPress\Forms\Filament\Resources\FormSubmissionResource\Pages\ViewFormSubmission;
use MiPress\Forms\Models\Form;
use MiPress\Forms\Models\FormSubmission;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static string|\BackedEnum|null $navigationIcon = 'fal-mailbox';

    protected static ?string $cluster = FormsCluster::class;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 31;

    public static function getModelLabel(): string
    {
        return __('mipress-forms::admin.resources.form_submission.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('mipress-forms::admin.resources.form_submission.plural_model_label');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('form_submission.view') === true;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getUnreadSubmissionsCount();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('mipress-forms::admin.resources.form_submission.unread_tooltip');
    }

    public static function getUnreadSubmissionsCount(): int
    {
        return once(fn (): int => static::getUnreadSubmissionsQuery()->count());
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['form', 'attachments']);
        $user = auth()->user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole(UserRole::SuperAdmin->value)) {
            return $query;
        }

        $userId = $user->getKey();

        return $query->whereHas('form', function (Builder $formQuery) use ($userId): void {
            $formQuery
                ->whereJsonContains('recipients', $userId)
                ->orWhereJsonContains('recipients', (string) $userId);
        });
    }

    protected static function getUnreadSubmissionsQuery(): Builder
    {
        $query = FormSubmission::query()->where('is_read', false);
        $user = auth()->user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole(UserRole::SuperAdmin->value)) {
            return $query;
        }

        $userId = $user->getKey();

        return $query->whereHas('form', function (Builder $formQuery) use ($userId): void {
            $formQuery
                ->whereJsonContains('recipients', $userId)
                ->orWhereJsonContains('recipients', (string) $userId);
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('form_id')
                ->label(__('mipress-forms::admin.resources.form_submission.fields.source_form'))
                ->options(Form::query()->orderBy('title')->pluck('title', 'id')->all())
                ->disabled(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('form.title')
                ->label(__('mipress-forms::admin.resources.form_submission.fields.source_form')),
            TextEntry::make('created_at')
                ->label(__('mipress-forms::admin.resources.form_submission.fields.received_at'))
                ->since(),
            TextEntry::make('is_read')
                ->label(__('mipress-forms::admin.resources.form_submission.fields.status'))
                ->formatStateUsing(fn (bool $state): string => $state ? __('mipress-forms::admin.resources.form_submission.states.read') : __('mipress-forms::admin.resources.form_submission.states.unread')),
            TextEntry::make('ip_address')
                ->label(__('mipress-forms::admin.resources.form_submission.fields.ip_address'))
                ->placeholder(__('mipress-forms::admin.common.empty')),
            TextEntry::make('user_agent')
                ->label(__('mipress-forms::admin.resources.form_submission.fields.user_agent'))
                ->placeholder(__('mipress-forms::admin.common.empty'))
                ->columnSpanFull(),
            TextEntry::make('submission_data')
                ->label(__('mipress-forms::admin.resources.form_submission.fields.message_content'))
                ->state(fn (FormSubmission $record): string => static::formatSubmissionDataAsHtml($record))
                ->html()
                ->columnSpanFull(),
            TextEntry::make('attachments_links')
                ->label(__('mipress-forms::admin.resources.form_submission.fields.attachments'))
                ->state(fn (FormSubmission $record): string => $record->attachments
                    ->map(fn ($attachment): string => sprintf(
                        '<a href="%s" class="text-primary-600 underline">%s</a>',
                        route('mipress.form.attachments.download', ['submission' => $record, 'attachment' => $attachment]),
                        e($attachment->filename),
                    ))
                    ->implode('<br>'))
                ->html()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form.title')->label(__('mipress-forms::admin.resources.form_submission.fields.source_form'))->searchable(),
                TextColumn::make('created_at')->label(__('mipress-forms::admin.resources.form_submission.fields.received_at'))->isoDateTime('LLL'),
                TextColumn::make('is_read')
                    ->label(__('mipress-forms::admin.resources.form_submission.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('mipress-forms::admin.resources.form_submission.states.read') : __('mipress-forms::admin.resources.form_submission.states.unread'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
                TextColumn::make('summary')
                    ->label(__('mipress-forms::admin.resources.form_submission.fields.message_preview'))
                    ->state(fn (FormSubmission $record): string => static::formatSubmissionPreviewAsHtml($record))
                    ->html()
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('form_id')
                    ->label(__('mipress-forms::admin.resources.form_submission.fields.source_form'))
                    ->options(Form::query()->orderBy('title')->pluck('title', 'id')->all()),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('markRead')
                        ->label(__('mipress-forms::admin.resources.form_submission.actions.mark_read'))
                        ->visible(fn (FormSubmission $record): bool => auth()->user()?->hasPermissionTo('form_submission.update') === true && ! $record->is_read)
                        ->action(function (FormSubmission $record): void {
                            $record->update([
                                'is_read' => true,
                                'read_by' => auth()->id(),
                                'read_at' => now(),
                            ]);
                        }),
                    Action::make('markUnread')
                        ->label(__('mipress-forms::admin.resources.form_submission.actions.mark_unread'))
                        ->visible(fn (FormSubmission $record): bool => auth()->user()?->hasPermissionTo('form_submission.update') === true && $record->is_read)
                        ->action(function (FormSubmission $record): void {
                            $record->update([
                                'is_read' => false,
                                'read_by' => null,
                                'read_at' => null,
                            ]);
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label(__('mipress-forms::admin.resources.form_submission.actions.mark_read'))
                        ->action(function ($records): void {
                            FormSubmission::query()
                                ->whereIn('id', $records->pluck('id'))
                                ->update([
                                    'is_read' => true,
                                    'read_by' => auth()->id(),
                                    'read_at' => now(),
                                ]);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormSubmissions::route('/'),
            'view' => ViewFormSubmission::route('/{record}'),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected static function getSubmissionFieldLabels(FormSubmission $record): array
    {
        return collect($record->form?->fields ?? [])
            ->mapWithKeys(function (array $field): array {
                $handle = (string) ($field['handle'] ?? '');

                if ($handle === '') {
                    return [];
                }

                $label = trim((string) ($field['label'] ?? ''));

                return [$handle => $label !== '' ? $label : $handle];
            })
            ->all();
    }

    protected static function formatSubmissionDataAsHtml(FormSubmission $record): string
    {
        $labels = static::getSubmissionFieldLabels($record);

        $rows = collect($record->data ?? [])
            ->filter(fn (mixed $value): bool => static::normalizeSubmissionValue($value) !== '')
            ->map(function (mixed $value, string $handle) use ($labels): string {
                $label = $labels[$handle] ?? $handle;
                $formattedValue = nl2br(e(static::normalizeSubmissionValue($value)));

                return sprintf(
                    '<div><span class="font-medium text-gray-700 dark:text-gray-300">%s:</span> <span class="text-gray-900 dark:text-gray-100">%s</span></div>',
                    e($label),
                    $formattedValue,
                );
            })
            ->values();

        if ($rows->isEmpty()) {
            return '<span class="text-gray-500">'.e(__('mipress-forms::admin.common.empty')).'</span>';
        }

        return '<div class="space-y-1">'.$rows->implode('').'</div>';
    }

    protected static function formatSubmissionPreviewAsHtml(FormSubmission $record): string
    {
        $labels = static::getSubmissionFieldLabels($record);

        $rows = collect($record->data ?? [])
            ->filter(fn (mixed $value): bool => static::normalizeSubmissionValue($value) !== '')
            ->take(3)
            ->map(function (mixed $value, string $handle) use ($labels): string {
                $label = $labels[$handle] ?? $handle;
                $normalizedValue = static::normalizeSubmissionValue($value);

                return sprintf(
                    '<div><span class="font-medium text-gray-700 dark:text-gray-300">%s:</span> <span class="text-gray-900 dark:text-gray-100">%s</span></div>',
                    e($label),
                    e(mb_strimwidth($normalizedValue, 0, 90, '...')),
                );
            })
            ->values();

        if ($rows->isEmpty()) {
            return '<span class="text-gray-500">'.e(__('mipress-forms::admin.common.empty')).'</span>';
        }

        return '<div class="space-y-1">'.$rows->implode('').'</div>';
    }

    protected static function normalizeSubmissionValue(mixed $value): string
    {
        return match (true) {
            $value instanceof BackedEnum => (string) $value->value,
            $value instanceof Htmlable => strip_tags($value->toHtml()),
            is_bool($value) => $value ? __('mipress-forms::admin.common.yes') : __('mipress-forms::admin.common.no'),
            is_scalar($value) => trim((string) $value),
            is_array($value) => collect($value)
                ->filter(fn (mixed $item): bool => is_scalar($item) && trim((string) $item) !== '')
                ->map(fn (mixed $item): string => (string) $item)
                ->implode(', '),
            default => '',
        };
    }
}
