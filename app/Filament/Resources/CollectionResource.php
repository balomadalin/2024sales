<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Filament\Resources\CollectionResource\RelationManagers;
use App\Models\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
//use Filament\Resources\Set;
use Filament\Forms\Components\Group;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\FormsGet;
use App\Models\Invoice;
use App\Models\Project;
use Filament\Forms\Components;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use Carbon\Carbon;
use Filament\Forms\Components\Toggle;
use App\Models\Client;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = ' Încasări';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Încasări și plăți';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columnSpan(12)->schema(function (Get $get, Set $set): array {
                    $schemas = [];

                    if ($get('invoice_id')) {
                        $invoice = Invoice::find($get('invoice_id'));
                        $set('amount_received', $invoice->total);
                    }

                    return $schemas;


                }),
                Section::make('Detalii Incasare')->columns(2)->schema([
                    Select::make('invoice_id')
                        ->relationship('invoices', 'id')
                        ->label('Număr factură')
                        ->preload()
                        ->required(),

                    DatePicker::make('start_at')
                        ->label('Data Incasare')
                        ->required(),

                    TextInput::make('amount_received')
                        ->live()
                        ->label('Valoare factura')
                        ->required()
                        ->dehydrated()
                        ->numeric()
                        ->default(0)

                        ->disabled(fn (Get $get): bool => !$get('invoice_id')),

                    Select::make('payment_method')
                        ->options([
                            'card' => 'Card',
                            'cont' => 'Cont',
                            'trezo' => 'Trezorerie',
                        ])
                        ->label('Plătit în ')
                        ->required(),

                    Textarea::make('details')
                        ->label('Detalii'),
                ]),
            ]);
    }




public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('invoices.id')->label('Număr factura')


                ->description(function ($record): string {
                    $invoices = $record->invoices ?? null;
                    // Check if $invoices is not null before attempting to call first()
                    return optional($invoices)->first()?->series ?? 'N/A';
            }),


            TextColumn::make('invoices.clients_id')->label('Client')

    ->description(function ($record): string {
        $invoices = $record->invoices ?? null;
        // Check if $invoices is not null before attempting to call first()
        return optional($invoices)->first()?->client->name ?? 'N/A';
    }),

            TextColumn::make('start_at')->label('Data Incasare'),
            TextColumn::make('amount_received')->label('Suma Incasata')->money('ron')
            ->fontFamily(FontFamily::Mono)
            ->alignment(Alignment::Center),
            TextColumn::make('payment_method')->label('Plătit in'),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
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
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
