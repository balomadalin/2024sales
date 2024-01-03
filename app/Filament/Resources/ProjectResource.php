<?php

namespace App\Filament\Resources;

use App\Enums\PricingUnit;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Toggle;



class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Vânzări';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Section::make()
                    ->columns(12)
                    ->schema([
                        Components\Select::make('clients_id')
                            ->label(trans_choice('client', 1))
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->suffixIcon('tabler-users')
                            ->columnSpan(6)
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->label('Nume')->required(),
                                TextInput::make('cui')->label('CUI/CNP')->required(),
                                TextInput::make('rc')->label('RC/CI')->required(),
                                TextInput::make('bank')->label('Nume Bancă')->nullable(),
                                TextInput::make('iban')->label('IBAN')->nullable(),
                                TextInput::make('phone')->label('Telefon')->nullable(),
                                TextInput::make('email')->label('Email')->nullable(),
                                TextInput::make('state')->label('Județ')->required(),
                                TextInput::make('city')->label('Localitate')->required(),
                                Textarea::make('address')->label('Adresă')->nullable(),
                                TextInput::make('person')->label('Persoană de contact')->nullable(),
                                TextInput::make('position')->label('Funcție')->nullable(),
                                Textarea::make('info')->label('Info')->nullable(),

                            ]),
                        Components\Toggle::make('aborted')
                            ->label(__('aborted'))
                            ->inline(false)
                            ->columnSpan(6),
                        Components\TextInput::make('title')
                            ->label(__('title'))
                            ->columnSpan(6)
                            ->required(),
                        Components\Textarea::make('description')
                            ->label(__('description'))
                            ->autosize()
                            ->maxLength(65535)
                            ->columnSpan(6),
                        Components\DatePicker::make('start_at')
                            ->label(__('startAt'))
                            ->weekStartsOnMonday()
                            ->suffixIcon('tabler-calendar-plus')
                            ->columnSpan(6),
                        Components\DatePicker::make('due_at')
                            ->label(__('dueAt'))
                            ->weekStartsOnMonday()
                            ->suffixIcon('tabler-calendar-check')
                            ->columnSpan(6),

                        Components\TextInput::make('total')
                            ->label(__('total'))
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0.01)
                            ->suffixIcon('tabler-currency-euro')
                            ->columnSpan(6)
                            ->required(),
                        Components\Select::make('pricing_unit')
                            ->label(__('pricingUnit'))
                            ->options(PricingUnit::class)
                            ->suffixIcon('tabler-clock-2')
                            ->columnSpan(6)
                            ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Columns\TextColumn::make('client.name')->label('Nume Client'),



                Columns\TextColumn::make('date_range')
                    ->label(__('dateRange'))
                    ->state(fn (Project $record): string => Carbon::parse($record->start_at)
                        ->longAbsoluteDiffForHumans(Carbon::parse($record->due_at), 2)
                    )
                    ->description(fn (Project $record): string => Carbon::parse($record->start_at)
                        ->isoFormat('ll') . ' - ' . Carbon::parse($record->due_at)->isoFormat('ll')
                    ),
              /*  Columns\TextColumn::make('scope')
                    ->label(__('scope'))
                    ->state(fn (Project $record): string => $record->scope_range)
                    ->description(fn (Project $record): string => $record->price_per_unit),*/
                Columns\TextColumn::make('progress')
                    ->label(__('progress'))
                    //->state(fn (Project $record): string => $record->hours_with_label)
                    ->description(fn (Project $record): string => $record->progress_percent),
                Columns\TextColumn::make('created_at')
                    ->label(__('createdAt'))
                    ->datetime('j. F Y, H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Columns\TextColumn::make('updated_at')
                    ->label(__('updatedAt'))
                    ->datetime('j. F Y, H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions(
                Actions\ActionGroup::make([
                    Actions\EditAction::make()->icon('tabler-edit'),
                    Actions\ReplicateAction::make()->icon('tabler-copy'),
                    Actions\Action::make('download')
                        ->label(__('quote'))
                        ->icon('tabler-file-type-pdf')
                        ->url(fn (Project $record): string => static::getUrl('download', ['record' => $record]))
                        ->openUrlInNewTab(),
                    Actions\DeleteAction::make()->icon('tabler-trash'),
                ])
                ->icon('tabler-dots-vertical')
            )
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()->icon('tabler-trash'),
                ])
                ->icon('tabler-dots-vertical'),
            ])
            ->emptyStateActions([
                Actions\CreateAction::make()->icon('tabler-plus'),
            ])
            ->emptyStateIcon('tabler-ban')
            ->defaultSort('due_at', 'desc')
            ->deferLoading();
    }

    public static function getRelations(): array
    {
        return [
        //    RelationManagers\EstimatesRelationManager::class,
         //   RelationManagers\InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'download' => Pages\DownloadQuote::route('/{record}/download'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('coreData');
    }

    public static function getNavigationLabel(): string
    {
        return trans_choice('project', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('project', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('project', 2);
    }

}
