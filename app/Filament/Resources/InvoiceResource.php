<?php

namespace App\Filament\Resources;

use App\Enums\PricingUnit;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
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
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Placeholder;
use App\Models\Products;
use App\Models\Facturi;
use App\Models\Company;
use App\Models\Setting;

use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Forms\Components\TagsInput;


use function Filament\Support\format_money;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'tabler-file-stack';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Vânzări';

    public static function form(Form $form): Form
    {
        $invoiceId = 'id';

        return $form
            ->columns(10)
            ->schema([
                Card::make()->schema([
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

                Card::make()->schema([
                    Section::make('Date facturare')->schema([
                        Card::make()->schema([
                            TextInput::make('series')->label('Serie')->default('WBX'),
                            TextInput::make('id')->label('Numar'),
                            DatePicker::make('start_at')->label('Data Emitere')->nullable(),
                            DatePicker::make('due_at')->label('Scadenta'),
                            Placeholder::make('total')
                            ->live()
                            ->label('Valoare factura')
                            ->content(function (Get $get) {
                                $invoiceId = $get('id');

                                // Obțineți factura și relația cu produsele
                                $invoice = Invoice::with('products')->find($invoiceId);

                                if (!$invoice) {
                                    return 'Invoice not found';
                                }

                                // Calculează suma 'valoareWithTvaAndDiscount' pentru fiecare produs și adună-le
                                $totalSum = $invoice->products->sum(function ($product) {
                                    $valoare = $product->unit_price * $product->quantity;
                                    $valoareWithDiscount = $valoare - ($valoare * ($product->discount / 100));
                                    $valoareWithTvaAndDiscount = $valoareWithDiscount * (1 + $product->tva / 100);
                                    return $valoareWithTvaAndDiscount;
                                });

                                // Actualizează totalul în entitatea Invoice
                                $invoice->total = $totalSum;

                                // Salvare în baza de date
                                $invoice->save();

                                return number_format($totalSum, 2) . 'RON';
                            })

                        ])->columnSpan(4),
                    ]),
                ]),

                Card::make()->schema([
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

        $invoiceId = 'id';
        return $table
            ->columns([
                Columns\TextColumn::make('client.name')->label('Nume Client'),


                Columns\TextColumn::make('series')
                    ->label(__('Număr factură'))

                    ->searchable()
                    ->sortable()
                   ->description(fn (Invoice $record): string => $record->id),
                Columns\TextColumn::make('total')
                    ->label(__('Valoare factura'))
                    ->money('ron')
                    ->fontFamily(FontFamily::Mono),
                Columns\TextColumn::make('date_range')
                    ->label(__('Scadenta'))
                    ->state(fn (Invoice $record): string => Carbon::parse($record->start_at)
                        ->longAbsoluteDiffForHumans(Carbon::parse($record->due_at), 2)
                    )
                    ->description(fn (Invoice $record): string => Carbon::parse($record->start_at)
                        ->isoFormat('ll') . ' - ' . Carbon::parse($record->due_at)->isoFormat('ll')
                    ),


            ])
            ->filters([
                //
            ])
            ->actions(
                Actions\ActionGroup::make([
                    Actions\ViewAction::make()->icon('tabler-eye'),
                    Actions\EditAction::make()->icon('tabler-edit'),
                    Actions\ReplicateAction::make()
                        ->icon('tabler-copy')
                        ->excludeAttributes(['invoiced_start_at', '']),
                    Actions\Action::make('download')
                        ->label(__('download'))
                        ->icon('tabler-file-type-pdf')
                        ->url(fn (Invoice $record): string => static::getUrl('download', ['record' => $record]))
                        ->openUrlInNewTab(),
                    Actions\Action::make('send')
                        ->label('Trimite mesaj')
                            ->icon('tabler-mail-forward')
                            ->form(fn (Invoice $record) => [
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
                    Actions\DeleteAction::make()->icon('tabler-trash'),
                ])->icon('tabler-dots-vertical')
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
            ->defaultSort('created_at', 'desc')
            ->deferLoading();
    }

    public static function getRelations(): array
    {
        return [
          //  RelationManagers\PositionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'download' => Pages\DownloadInvoice::route('/{record}/download'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('coreData');
    }

    public static function getNavigationLabel(): string
    {
        return trans_choice('invoice', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('invoice', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('invoice', 2);
    }
    public static function getNextInvoiceNumber()
    {
        $lastId = Invoice::latest()->value('id');
        $nextInvoiceNumber = ''   . $lastId + 00001;

        return $nextInvoiceNumber;
    }

    public function livewireScripts()
    {
        return [
            "Livewire.on('product_value.updated', () => {
                Livewire.emit('updateTotal');
            })",
        ];
    }
}
