<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QarorlarResource\Pages;
use App\Imports\QarorlarImport;
use App\Models\Qaror;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

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
                            TextInput::make('title')
                                ->label('Nomlanishi')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('created_date')
                                ->label('Qaror chiqarilgan sana')
                                ->suffixIcon('heroicon-o-calendar')
                                ->displayFormat('d.m.Y')
                                ->maxDate(now())
                                ->placeholder('26.12.2025')
                                ->native(false),

                            TextInput::make('number')
                                ->label('Qaror raqami')
                                ->required()
                                ->placeholder('2055'),
                        ]),

                        FileUpload::make('pdf_path')
                            ->label('PDF fayl')
                            ->disk('public')
                            ->directory('qarorlar')
                            ->acceptedFileTypes(['application/pdf'])
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable(),

                    ])
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->rowIndex(false),
                TextColumn::make('title')
                    ->limit(80)
                    ->label('Nomlanishi')
                    ->searchable(),
                TextColumn::make('number')
                    ->label('Qaror raqami')
                    ->formatStateUsing(fn ($state) => '№ '.$state)
                    ->searchable(),
                TextColumn::make('created_date')
                    ->date('d.m.Y')
                    ->label('Qaror chiqgan sana')
                    ->sortable(),
                TextColumn::make('views')
                    ->label('Ko\'rishlar')
                    ->formatStateUsing(function ($state) {
                        if ($state >= 1000) {
                            return round($state / 1000, 1) . 'k';
                        }
                        return $state;
                    })
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('pdf_path')
                    ->label('PDF')
                    ->url(fn ($record) => $record->pdf_path ? url('/pdfs/'.$record->number) : null)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? 'PDF' : '-'),
            ])
            ->headerActions([
                Action::make('excelImport')
                    ->label('Excel yuklash')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalWidth('md')
                    ->closeModalByClickingAway(false)
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
                    ->action(function (array $data, Action $action) {
                        try {
                            $absolutePath = storage_path('app/private/'.$data['file']);
                            Excel::import(new QarorlarImport, $absolutePath);

                            Notification::make()
                                ->title('Excel muvaffaqiyatli yuklandi!')
                                ->success()
                                ->send();

                            // Redirect to avoid Livewire DOM unmounting error
                            redirect(static::getUrl('index'));
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Xatolik yuz berdi')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->filters([
                Filter::make('title')
                    ->form([
                        TextInput::make('title')
                            ->label('Nomlanishi'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['title'] ?? null,
                        fn ($q, $title) => $q->where('title', 'like', "%{$title}%")
                    )
                    ),

                Filter::make('number')
                    ->form([
                        TextInput::make('number')
                            ->label('Qaror raqami'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['number'] ?? null,
                        fn ($q, $number) => $q->where('number', 'like', "%{$number}%")
                    )
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
            'index' => Pages\ListQarorlar::route('/'),
            'create' => Pages\CreateQarorlar::route('/create'),
            'edit' => Pages\EditQarorlar::route('/{record}/edit'),
        ];
    }
}
