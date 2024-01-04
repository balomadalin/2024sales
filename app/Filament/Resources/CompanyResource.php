<?php

namespace App\Filament\Resources;
use Filament\Forms;
use Filament\Tables;
use App\Models\Company;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\CompanyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CompanyResource\RelationManagers;
use Filament\Notifications\Notification;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Companie';
    protected static ?string $navigationGroup = '';

    public static function sendNotication($title = "Errors found!", $message, $severity = 'success')
    {
        Log::info('Sending notification');
        Notification::make()
            ->$severity()
            ->title($title)
            ->body($message)
            ->send();
    }


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()

                ->schema([
                    Section::make('Date firmă')->columns(12)
                        ->schema([
                            Card::make()
                                ->schema([
                                    Wizard::make()
                                        ->schema([
                                            Step::make('General')
                                                ->schema([
                                                    FileUpload::make('logo')
                                                        ->image()
                                                        ->imageResizeMode('cover')
                                                        ->imageCropAspectRatio('16:9')
                                                        ->imageResizeTargetWidth('1920')
                                                        ->imageResizeTargetHeight('1080'),
                                                    TextInput::make('name')->label('Denumire firmă')
                                                        ->required(),
                                                        TextInput::make('cui')->label('CUI')
                                                        ->required(),
                                                        TextInput::make('rc')->label('Nr. Reg. Comerț')
                                                        ->required(),
                                                        TextInput::make('bank')->label('Nume Bancă'),
                                                        TextInput::make('iban')->label('IBAN'),
                                                ]),
                                            Step::make('Date de contact')
                                                ->schema([
                                                    TextInput::make('email')
                                                        ->required()
                                                        ->email(),
                                                    Grid::make(2)
                                                        ->schema([

                                                            TextInput::make('phone')
                                                                ->required()
                                                                ->tel(),
                                                        ]),
                                                ]),
                                            Step::make('Adresă')
                                                ->schema([
                                                    Textarea::make('state'),
                                                    TextInput::make('city'),
                                                    TextInput::make('address'),
                                                ]),
                                            Step::make('Contact Person')
                                                ->schema([

                                                            TextInput::make('person')
                                                                ->required(),

                                                            TextInput::make('position')
                                                                ->required(),

                                                ]),
                                        ]),
                                ]),
                        ])
                        ->columnSpan(8),

                ]),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            ImageColumn::make('logo')
                ->circular()
                ->stacked()
                ->limit(3),
            TextColumn::make('name')->label('Denumire Firmă')
                ->searchable()
                ->toggleable()
                ->sortable(),
            TextColumn::make('email')
                ->searchable()
                ->toggleable(),
            TextColumn::make('phone')
                ->searchable()
                ->toggleable(),

        ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('set_company')
                    ->requiresConfirmation()
                    ->action(function (Company $record) {
                        $company = $record->toArray();

                        try {
                            session(['company' => $company]);
                            self::sendNotication('Company set', 'Company set successfully', 'success');
                        }
                        catch (\Exception $e) {
                            self::sendNotication('Error', $e->getMessage(), 'danger');
                        }
                    }),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
