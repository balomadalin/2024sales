<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstimateResource\Pages;
use App\Models\Estimate;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
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
use Filament\Forms\Get;


class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Oferte';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationGroup = 'Vânzări';


    public static function form(Form $form): Form
    {
        return $form
->schema([
    Card::make()->columnSpan(12)
                            ->schema([
                 Select::make('clients_id')->label('Selectează client')->relationship('clients', 'name')->searchable()->preload()
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
                ])->columnSpan(4),
                ]),

                    Grid::make()->columnSpan(12)
                    ->schema([
                        Section::make('Date ofertare')
                            ->schema([
                                Card::make()
                                    ->schema([
                TextInput::make('series')->label('Serie')->default('WBX-Ofertă'),
                TextInput::make('id')->label('Numar')->default(static::getNextOfertaNumber()),
                DatePicker::make('start_at')->label('Data Emitere')->nullable(),

                DatePicker::make('due_at')->label('Valabili până la')->nullable()
                ])->columnSpan(4),

            ])  ]),
            Card::make()->columnSpan(12)
            ->schema([
                Repeater::make('products')->columnSpan(12)->label('Produse')->relationship()
                    ->schema([
                        TextInput::make('name_product')->label('Nume Produs')->columnSpan(8),
                        TextInput::make('description_product')->label('Descriere produs')->columnSpan(4),
                        Select::make('unit')->label('UM')
                        ->options([
                            'bac' => 'Bucată',
                            'an' => 'An',
                            'luni' => 'Luni',
                            'bax' => 'Bax',
                        ])->columnSpan(3),
                        Select::make('tva')->label('TVA')->live()
                        ->options([
                            '0' => '0',
                            '5' => '5',
                            '9' => '9',
                            '19' => '19'])


                            ->nullable()
                        ->columnSpan(1),

                        TextInput::make('quantity')->label('Cantitate')
                        ->reactive()
                        ->numeric()
                        ->default(1)
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            $pretUnitar = $get('unit_price');
                            $cantitate = $state;

                            $valoare = ($pretUnitar * $cantitate);
                            $set('product_value', $valoare);

                            $discount = $get('discount');

                            $tva = $get('tva');

                            $totalfaratva = $valoare * (1 + $tva / 100);
                            $totalcudiscount = $totalfaratva - ($totalfaratva * $discount / 100);
                            $set('total', $totalcudiscount);

                        })->columnSpan(1),
                        TextInput::make('unit_price')->label('Pret Unitar') ->reactive()->numeric() ->live()->placeholder('10.5')->suffix('lei')
                        ->columnSpan(4),

                        TextInput::make('discount')->live()->label('Discount')->suffix('%')->placeholder('0.5')->numeric()->nullable()->columnSpan(1),


                        Placeholder::make('product_value')
                            ->label('Total')
                            ->live()
                            ->content(function (Get $get): string {
                                $pretUnitar = $get('unit_price');
                                $cantitate = $get('quantity');
                                $discount = $get('discount');
                                $tva = $get('tva');

                                $valoare = $pretUnitar * $cantitate;
                                $valoareWithDiscount = $valoare - ($valoare * ($discount / 100));
                                $valoareWithTvaAndDiscount = $valoareWithDiscount * (1 + $tva / 100);

                                return number_format($valoareWithTvaAndDiscount, 2) . 'RON';
                            })

                        ->columnSpan(1),

                    ]),
            ]),

        ]);
    }

  public function save($model, Form $form)
  {
      // Save the parent model
      parent::save($model, $form);

      // Save the products
      $productsData = $form->get('products');

      if ($productsData) {
          foreach ($productsData as $productData) {
              // Use the correct model class (Product) instead of product
              $product = new Product([
                  'name_product' => $productData['name_product'],
                  'description_product' => $productData['description_product'],
                  'unit' => $productData['unit'],
                  'tva' => $productData['tva'],
                  'quantity' => $productData['quantity'],
                  'unit_price' => $productData['unit_price'],
                  'product_value' => $productData['product_value'],
                  'discount' => $productData['discount'],
              ]);

              $product->save();

              // Use the correct relationship method (products instead of product)
              $model->products()->save($product);
          }
      }
  }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('series')->label('Serie'),
                TextColumn::make('id')->label('Numar'),
                TextColumn::make('start_at')->label('Data Emitere'),
                TextColumn::make('due_at')->label('Valabili până la'),
                TextColumn::make('client.name')->label('Nume Client'),


            ])
            ->filters([
                //
            ])
            ->actions(
                Actions\ActionGroup::make([
                    Actions\EditAction::make()->icon('tabler-edit'),
                    Actions\ReplicateAction::make()->icon('tabler-copy'),
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
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEstimates::route('/'),
            //'index' => Pages\ListEstimates::route('/'),
        //    'create' => Pages\CreateEstimates::route('/create'),
         //   'view' => Pages\ViewEstimates::route('/{record}'),
         //   'edit' => Pages\EditEstimatese::route('/{record}/edit'),
           //'download' => Pages\DownloadEstimates::route('/{record}/download'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('coreData');
    }

    public static function getNavigationLabel(): string
    {
        return trans_choice('estimate', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('estimate', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('estimate', 2);
    }

    public static function getNextOfertaNumber()
    {
        $lastId = Estimate::latest()->value('id');
        $nextInvoiceNumber = ''   . $lastId + 00001;

        return $nextInvoiceNumber;
    }
}
