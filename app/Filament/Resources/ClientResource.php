<?php

namespace App\Filament\Resources;

use App\Enums\LanguageCode;
use App\Filament\Resources\ClientResource\Pages;
use App\Mail\ContactClient;
use App\Models\Client;
use App\Models\Setting;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Vânzări';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
             Card::make('Completeaz date client')
            ->columns(3)->schema([
            TextInput::make('name')->label('Nume')->required(),
            TextInput::make('cui')->label('CUI/CNP')->required(),
            TextInput::make('rc')->label('rc/ci')->required(),
            TextInput::make('bank')->label('Nume Bancă')->nullable(),
            TextInput::make('iban')->label('IBAN')->nullable(),
            TextInput::make('phone')->label('Telefon')->nullable(),
            TextInput::make('email')->label('Email')->nullable(),
            TextInput::make('state')->label('Județ')->required(),
            TextInput::make('city')->label('Localitate')->required(),
            Textarea::make('address')->label('Adresă')->nullable(),
            TextInput::make('person')->label('Persoană de contact')->nullable(),
            TextInput::make('position')->label('Funcție')->nullable(),
            Textarea::make('info')->label('Info')->nullable()->columnspan(4),
            ]),
        ]);

}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Columns\TextColumn::make('name')
                    ->label(__('name'))
                    ->searchable()
                    ->sortable()
                  // ->description(fn (Client $record): string => $record->address)
                    ->wrap(),

                Columns\TextColumn::make('cui')
                    ->label(__('CUI/CNP'))
                    ->fontFamily(FontFamily::Mono),
                   // ->state(fn (Client $record): float => $record->net)
                   // ->description(fn (Client $record): string => $record->hours . ' ' . trans_choice('hour', $record->hours)),
                Columns\TextColumn::make('rc')
                   ->label(__('rc/ci'))
                   ->fontFamily(FontFamily::Mono),
                Columns\TextColumn::make('phone')
                    ->label(__('phone')),

                Columns\TextColumn::make('email')
                    ->label(__('email')),

            ])
            ->filters([
                Filters\SelectFilter::make('language')
                    ->label(__('language'))
                    ->options(LanguageCode::class),
            ])
            ->actions(Actions\ActionGroup::make([
                Actions\EditAction::make()->icon('tabler-edit'),
                Actions\Action::make('kontaktieren')
                ->label('Trimite mesaj')
                    ->icon('tabler-mail-forward')
                    ->form(fn (Client $record) => [
                        Components\TextInput::make('subject')
                            ->label(__('subject'))
                            ->required(),
                        Components\RichEditor::make('content')
                            ->label(__('content'))
                            ->required()
                            ->default(__("email.template.contact.body", [
                                'name' => $record->name,
                                'sender' => Setting::get('name')
                            ])),
                    ])
                    ->action(function (Client $record, array $data) {
                        Mail::to($record->email)->send(
                            (new ContactClient(body: $data['content']))->subject($data['subject'])
                        );
                    }),
                Actions\ReplicateAction::make()->icon('tabler-copy'),
                Actions\DeleteAction::make()->icon('tabler-trash'),
            ]))
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
            ->defaultSort('created_at', 'desc')
            ->deferLoading();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('coreData');
    }

    public static function getNavigationLabel(): string
    {
        return trans_choice('client', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('client', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('client', 2);
    }
}
