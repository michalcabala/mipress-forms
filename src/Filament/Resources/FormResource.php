<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Resources;

use App\Models\User;
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
use MiPress\Forms\Filament\Clusters\FormsCluster;
use MiPress\Forms\Enums\FormFieldType;
use MiPress\Forms\Enums\SpamProtectionMode;
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

    protected static ?string $modelLabel = 'Formulář';

    protected static ?string $pluralModelLabel = 'Formuláře';

    protected static ?int $navigationSort = 30;

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
            Section::make('Základní nastavení')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('template')
                                ->label('Začít ze šablony')
                                ->visibleOn('create')
                                ->dehydrated(false)
                                ->default('none')
                                ->options([
                                    'none' => 'Prázdný formulář',
                                    'contact' => 'Kontaktní formulář',
                                ])
                                ->live()
                                ->afterStateUpdated(function (?string $state, callable $set): void {
                                    $set('fields', match ($state) {
                                        'contact' => FormField::contactTemplate(),
                                        default => [],
                                    });
                                }),
                            Toggle::make('is_active')
                                ->label('Aktivní')
                                ->default(true),
                            TextInput::make('title')
                                ->label('Název')
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
                                ->label('Identifikátor')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->disabledOn('edit')
                                ->dehydrated(static fn (string $operation): bool => $operation === 'create'),
                        ]),
                    Textarea::make('description')
                        ->label('Popis')
                        ->rows(3),
                ]),

            Section::make('Pole formuláře')
                ->schema([
                    Repeater::make('fields')
                        ->label('Pole')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('handle')
                                        ->label('Identifikátor')
                                        ->required(),
                                    TextInput::make('label')
                                        ->label('Popisek')
                                        ->required(),
                                    Select::make('type')
                                        ->label('Typ')
                                        ->options(FormFieldType::options())
                                        ->live()
                                        ->required(),
                                    Toggle::make('required')
                                        ->label('Povinné')
                                        ->default(false),
                                    TextInput::make('order')
                                        ->label('Pořadí')
                                        ->numeric()
                                        ->default(0),
                                    TextInput::make('config.placeholder')
                                        ->label('Zástupný text')
                                        ->visible(fn (Get $get): bool => in_array((string) $get('type'), [
                                            FormFieldType::Text->value,
                                            FormFieldType::Email->value,
                                            FormFieldType::Phone->value,
                                            FormFieldType::Textarea->value,
                                            FormFieldType::Select->value,
                                        ], true)),
                                    TextInput::make('config.max_length')
                                        ->label('Max délka')
                                        ->numeric()
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::Text->value),
                                    TextInput::make('config.rows')
                                        ->label('Počet řádků')
                                        ->numeric()
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::Textarea->value),
                                    TextInput::make('config.max_size_mb')
                                        ->label('Max velikost (MB)')
                                        ->numeric()
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::File->value),
                                    TextInput::make('config.accepted')
                                        ->label('Povolené přípony')
                                        ->helperText('.pdf,.jpg,.png')
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::File->value),
                                    TextInput::make('config.value')
                                        ->label('Skrytá hodnota')
                                        ->visible(fn (Get $get): bool => (string) $get('type') === FormFieldType::Hidden->value),
                                    KeyValue::make('config.options')
                                        ->label('Možnosti')
                                        ->keyLabel('Klíč')
                                        ->valueLabel('Popisek')
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

            Section::make('Příjemci')
                ->schema([
                    Select::make('recipients')
                        ->label('Upozornit uživatele')
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

            Section::make('Ochrana proti spamu')
                ->schema([
                    Select::make('spam_protection')
                        ->label('Režim')
                        ->options(SpamProtectionMode::options())
                        ->default(SpamProtectionMode::Honeypot->value)
                        ->live()
                        ->required(),
                    TextInput::make('recaptcha_site_key')
                        ->label('reCAPTCHA veřejný klíč')
                        ->visible(fn (Get $get): bool => in_array((string) $get('spam_protection'), [
                            SpamProtectionMode::Recaptcha->value,
                            SpamProtectionMode::Both->value,
                        ], true)),
                    TextInput::make('recaptcha_secret_key')
                        ->label('reCAPTCHA tajný klíč')
                        ->password()
                        ->revealable()
                        ->visible(fn (Get $get): bool => in_array((string) $get('spam_protection'), [
                            SpamProtectionMode::Recaptcha->value,
                            SpamProtectionMode::Both->value,
                        ], true)),
                ]),

            Section::make('Automatická odpověď')
                ->schema([
                    Toggle::make('auto_reply_enabled')
                        ->label('Zapnout automatickou odpověď')
                        ->live(),
                    TextInput::make('auto_reply_subject')
                        ->label('Předmět')
                        ->visible(fn (Get $get): bool => (bool) $get('auto_reply_enabled')),
                    Textarea::make('auto_reply_body')
                        ->label('Text zprávy')
                        ->rows(5)
                        ->visible(fn (Get $get): bool => (bool) $get('auto_reply_enabled')),
                ]),

            Section::make('Potvrzovací zpráva')
                ->schema([
                    Textarea::make('success_message')
                        ->label('Zpráva po odeslání')
                        ->default('Děkujeme, formulář byl odeslán.')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Název')->searchable(),
                TextColumn::make('handle')->label('Identifikátor')->searchable(),
                TextColumn::make('unread_submissions_count')
                    ->label('Nepřečtené zprávy')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('is_active')
                    ->label('Aktivní')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ano' : 'Ne')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextColumn::make('updated_at')->label('Upraveno')->since(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
