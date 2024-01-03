<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Str;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Companie';
    protected static ?string $navigationGroup = '';
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
