<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Resources;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use MiPress\Core\Enums\UserRole;
use MiPress\Forms\Enums\FormFieldType;
use MiPress\Forms\Enums\SpamProtectionMode;
use MiPress\Forms\Filament\Clusters\FormsCluster;
use MiPress\Forms\Filament\Resources\FormResource\Pages\CreateForm;
use MiPress\Forms\Filament\Resources\FormResource\Pages\EditForm;
use MiPress\Forms\Filament\Resources\FormResource\Pages\ListForms;
use MiPress\Forms\Models\Form;
use MiPress\Forms\Models\FormField;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static string|\BackedEnum|null $navigationIcon = 'fal-clipboard-list';

    protected static ?string $cluster = FormsCluster::class;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string
    {
        return __('mipress-forms::admin.resources.form.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('mipress-forms::admin.resources.form.plural_model_label');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('form.view') === true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount([
            'submissions as unread_submissions_count' => fn (Builder $query): Builder => $query->where('is_read', false),
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('mipress-forms::admin.resources.form.sections.basic_settings'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('template')
                                ->label(__('mipress-forms::admin.resources.form.fields.template'))
                                ->visibleOn('create')
                                ->dehydrated(false)
                                ->default('none')
                                ->options([
                                    'none' => __('mipress-forms::admin.resources.form.options.template.none'),
                                    'contact' => __('mipress-forms::admin.resources.form.options.template.contact'),
                                ])
                                ->live()
                                ->afterStateUpdated(function (?string $state, callable $set): void {
                                    $set('fields', match ($state) {
                                        'contact' => FormField::contactTemplate(),
                                        default => [],
                                    });
                                }),
                            Toggle::make('is_active')
                                ->label(__('mipress-forms::admin.resources.form.fields.is_active'))
                                ->default(true),
                            TextInput::make('title')
                                ->label(__('mipress-forms::admin.resources.form.fields.title'))
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, callable $set, string $operation): void {
                                    if ($operation !== 'create' || blank($state)) {
                                        return;
                                    }

                                    $set('handle', Str::slug($state));
                                }),
                            TextInput::make('handle')
                                ->label(__('mipress-forms::admin.resources.form.fields.handle'))
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->disabledOn('edit')
                                ->dehydrated(static fn (string $operation): bool => $operation === 'create'),
                        ]),
                    Textarea::make('description')
                        ->label(__('mipress-forms::admin.resources.form.fields.description'))
                        ->rows(3),
                ]),

            Section::make(__('mipress-forms::admin.resources.form.sections.fields'))
                ->schema([
                    Repeater::make('fields')
                        ->label(__('mipress-forms::admin.resources.form.fields.form_fields'))
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('handle')
                                        ->label(__('mipress-forms::admin.resources.form.fields.handle'))
                                        ->required(),
                                    TextInput::make('label')
                                        ->label(__('mipress-forms::admin.resources.form.fields.label'))
                                        ->required(),
                                    Select::make('type')
                                        ->label(__('mipress-forms::admin.resources.form.fields.type'))
                                        ->options(FormFieldType::options())
                                        ->live()
                                        ->required(),
                                    Toggle::make('required')
                                        ->label(__('mipress-forms::admin.resources.form.fields.required'))
                                        ->default(false),
                                    TextInput::make('order')
                                        ->label(__('mipress-forms::admin.resources.form.fields.order'))
                                        ->numeric()
                                        ->default(0),
                                    TextInput::make('config.placeholder')
                                        ->label(__('mipress-forms::admin.resources.form.fields.placeholder'))
                                        ->visible(fn (Get $get): bool => in_array((string) $get('type'), [
                                            FormFieldType::Text->value,
                                            FormFieldType::Email->value,
                                            FormFieldType::Phone->value,
                                            FormFieldType::Textarea->value,
                                            FormFieldType::Select->value,
                                        ], true)),
                                    TextInput::make('config.max_length')
                                        ->label(__('mipress-forms::admin.resources.form.fields.max_length'))
                                        ->numeric()
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::Text->value),
                                    TextInput::make('config.rows')
                                        ->label(__('mipress-forms::admin.resources.form.fields.rows'))
                                        ->numeric()
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::Textarea->value),
                                    TextInput::make('config.max_size_mb')
                                        ->label(__('mipress-forms::admin.resources.form.fields.max_size_mb'))
                                        ->numeric()
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::File->value),
                                    TextInput::make('config.accepted')
                                        ->label(__('mipress-forms::admin.resources.form.fields.accepted'))
                                        ->helperText(__('mipress-forms::admin.resources.form.help.accepted'))
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::File->value),
                                    TextInput::make('config.value')
                                        ->label(__('mipress-forms::admin.resources.form.fields.hidden_value'))
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::Hidden->value),
                                    KeyValue::make('config.options')
                                        ->label(__('mipress-forms::admin.resources.form.fields.options'))
                                        ->keyLabel(__('mipress-forms::admin.resources.form.key_value.key_label'))
                                        ->valueLabel(__('mipress-forms::admin.resources.form.key_value.value_label'))
                                        ->visible(fn (Get $get): bool => in_array((string) $get('type'), [
                                            FormFieldType::Select->value,
                                            FormFieldType::Radio->value,
                                        ], true))
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->reorderable()
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ]),

            Section::make(__('mipress-forms::admin.resources.form.sections.recipients'))
                ->schema([
                    Select::make('recipients')
                        ->label(__('mipress-forms::admin.resources.form.fields.notify_users'))
                        ->multiple()
                        ->searchable()
                        ->options(fn (): array => User::query()
                            ->whereHas('roles', fn (Builder $query): Builder => $query->whereIn('name', [
                                UserRole::SuperAdmin->value,
                                UserRole::Admin->value,
                                UserRole::Editor->value,
                            ]))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all()),
                ]),

            Section::make(__('mipress-forms::admin.resources.form.sections.spam_protection'))
                ->schema([
                    Select::make('spam_protection')
                        ->label(__('mipress-forms::admin.resources.form.fields.spam_mode'))
                        ->options(SpamProtectionMode::options())
                        ->default(SpamProtectionMode::Honeypot->value)
                        ->live()
                        ->required(),
                    TextInput::make('recaptcha_site_key')
                        ->label(__('mipress-forms::admin.resources.form.fields.recaptcha_site_key'))
                        ->visible(fn (Get $get): bool => in_array((string) $get('spam_protection'), [
                            SpamProtectionMode::Recaptcha->value,
                            SpamProtectionMode::Both->value,
                        ], true)),
                    TextInput::make('recaptcha_secret_key')
                        ->label(__('mipress-forms::admin.resources.form.fields.recaptcha_secret_key'))
                        ->password()
                        ->revealable()
                        ->visible(fn (Get $get): bool => in_array((string) $get('spam_protection'), [
                            SpamProtectionMode::Recaptcha->value,
                            SpamProtectionMode::Both->value,
                        ], true)),
                ]),

            Section::make(__('mipress-forms::admin.resources.form.sections.auto_reply'))
                ->schema([
                    Toggle::make('auto_reply_enabled')
                        ->label(__('mipress-forms::admin.resources.form.fields.auto_reply_enabled'))
                        ->live(),
                    TextInput::make('auto_reply_subject')
                        ->label(__('mipress-forms::admin.resources.form.fields.auto_reply_subject'))
                        ->visible(fn (Get $get): bool => (bool) $get('auto_reply_enabled')),
                    Textarea::make('auto_reply_body')
                        ->label(__('mipress-forms::admin.resources.form.fields.auto_reply_body'))
                        ->rows(5)
                        ->visible(fn (Get $get): bool => (bool) $get('auto_reply_enabled')),
                ]),

            Section::make(__('mipress-forms::admin.resources.form.sections.success_message'))
                ->schema([
                    Textarea::make('success_message')
                        ->label(__('mipress-forms::admin.resources.form.fields.success_message'))
                        ->default(__('mipress-forms::admin.resources.form.defaults.success_message'))
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('mipress-forms::admin.resources.form.table.columns.title'))->searchable(),
                TextColumn::make('handle')->label(__('mipress-forms::admin.resources.form.table.columns.handle'))->searchable(),
                TextColumn::make('unread_submissions_count')
                    ->label(__('mipress-forms::admin.resources.form.table.columns.unread_submissions'))
                    ->badge()
                    ->color('warning'),
                TextColumn::make('is_active')
                    ->label(__('mipress-forms::admin.resources.form.table.columns.is_active'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('mipress-forms::admin.common.yes') : __('mipress-forms::admin.common.no'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextColumn::make('updated_at')
                    ->label(__('mipress-forms::admin.resources.form.table.columns.updated_at'))
                    ->isoDateTime('LLL')
                    ->description(fn ($record): ?string => filled($record->created_at) && filled($record->updated_at) && $record->updated_at->gt($record->created_at)
                        ? __('mipress-forms::admin.common.created_at_description', ['date' => $record->created_at->isoFormat('LLL')])
                        : null),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }
}
