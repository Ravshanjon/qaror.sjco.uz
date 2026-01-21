<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QarorlarResource\Pages;
use App\Filament\Admin\Resources\QarorlarResource\RelationManagers;
use App\Models\Qaror;
use App\Models\Qarorlar;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QarorlarImport;

class QarorlarResource extends Resource
{
    protected static ?string $model = Qaror::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('title')->label('Номланиши')
                                ->columnSpanFull(),

                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('created_date')
                                ->label('Қарор чиқарилган сана')
                                ->suffixIcon('heroicon-o-calendar')
                                ->displayFormat('d.m.Y')
                                ->maxDate(now())
                                ->placeholder('26.12.2025')
                                ->columns(1)
                                ->native(false),

                            TextInput::make('number')->label('Қарор рақами')
                                ->placeholder('2055')
                                ->columnSpan(1),

                        ]),
                        TextInput::make('published_id')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn() => random_int(10000, 99999))
                            ->label('Public ID'),

                        FileUpload::make('pdf_path')
                            ->label('PDF fayl')
                            ->disk('public')
                            ->directory('qarorlar')
                            ->acceptedFileTypes(['application/pdf'])
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()

                    ])
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->rowIndex(false),
                TextColumn::make('title')->limit(80)->label('Nomlanishi'),
                TextColumn::make('number')
                    ->label('Qaror raqami')
                    ->formatStateUsing(fn($state) => '№ ' . $state),
                TextColumn::make('created_at')
                    ->date('d.m.Y')
                    ->label('Qaror chiqgan sana'),

                TextColumn::make('pdf_path')
                    ->label('PDF')
                    ->url(fn($record) => url('/pdfs/' . $record->number))
                    ->openUrlInNewTab()
                    ->badge('success')
                    ->formatStateUsing(fn() => 'PDF'),
            ])
            ->headerActions([
                Action::make('excelImport')
                    ->label('Excel yuklash')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->label('Excel fayl')
                            ->required()
                            ->disk('local')
                            ->directory('imports')
                            ->storeFiles()
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $absolutePath = storage_path('app/private/' . $data['file']);
                        Excel::import(new QarorlarImport, $absolutePath);
                    })
            ])
            ->filters([
                Filter::make('title')
                    ->form([
                        TextInput::make('title')
                            ->label('Nomlanishi'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when(
                        $data['title'] ?? null,
                        fn($q, $title) => $q->where('title', 'like', "%{$title}%")
                    )
                    ),

                Filter::make('number')
                    ->form([
                        TextInput::make('number')
                            ->label('Qaror raqami'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when(
                        $data['number'] ?? null,
                        fn($q, $number) => $q->where('number', 'like', "%{$number}%")
                    )
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListQarorlars::route('/'),
            'create' => Pages\CreateQarorlar::route('/create'),
            'edit' => Pages\EditQarorlar::route('/{record}/edit'),
        ];
    }
}
