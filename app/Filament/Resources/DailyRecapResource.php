<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyRecapResource\Pages;
use App\Models\DailyRecap;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class DailyRecapResource extends Resource
{
    protected static ?string $model = DailyRecap::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('recap_date')
                    ->label('Tanggal Rekap')
                    ->required()
                    ->unique(ignoreRecord: true),

                Section::make('Data Sesi & Klien')
                    ->schema([
                        TextInput::make('session_count')
                            ->label('Jumlah Sesi Praktek')
                            ->numeric()
                            ->required()
                            ->default(0),
                        TextInput::make('new_chats')
                            ->label('Chat Masuk Baru')
                            ->numeric()
                            ->required()
                            ->default(0),
                        TextInput::make('new_client_goals')
                            ->label('Goals Klien Baru')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ])->columns(3),

                Section::make('Sumber Klien & Ulasan')
                    ->schema([
                        TextInput::make('gmap_reviews')
                            ->label('Ulasan Google Map')
                            ->numeric()
                            ->required()
                            ->default(0),
                        TextInput::make('source_tiktok')
                            ->label('Dari TikTok')
                            ->numeric()
                            ->required()
                            ->default(0),
                        TextInput::make('source_google')
                            ->label('Dari Google')
                            ->numeric()
                            ->required()
                            ->default(0),
                        TextInput::make('source_instagram')
                            ->label('Dari Instagram')
                            ->numeric()
                            ->required()
                            ->default(0),
                        TextInput::make('source_friend')
                            ->label('Dari Teman/Kerabat')
                            ->numeric()
                            ->required()
                            ->default(0),
                        TextInput::make('jam_gandeng')
                            ->label('Jam Gandeng')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ])->columns(2),

                Textarea::make('extra_notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            TextColumn::make('recap_date')
                ->label('Tanggal Rekap')
                ->date()
                ->sortable(),

            TextColumn::make('session_count')
                ->label('Jumlah Sesi Praktek')
                ->sortable(),

            TextColumn::make('new_chats')
                ->label('Chat Masuk Baru'),

            TextColumn::make('new_client_goals')
                ->label('Goals Klien Baru'),

            TextColumn::make('gmap_reviews')
                ->label('Ulasan Google Map'),

            TextColumn::make('source_tiktok')
                ->label('Dari TikTok'),

            TextColumn::make('source_google')
                ->label('Dari Google'),

            TextColumn::make('source_instagram')
                ->label('Dari Instagram'),

            TextColumn::make('source_friend')
                ->label('Dari Teman/Kerabat'),

            TextColumn::make('jam_gandeng')
                ->label('Jam Gandeng'),

            TextColumn::make('created_at')
                ->label('Dibuat Pada')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ])
        ->headerActions([
            ExportAction::make('exportExcel')
                ->label('Export Excel')
                ->exports([
                    ExcelExport::make('form')
                        ->fromForm()
                        ->withFilename('Rekap Harian -' . now()->format('Y-m-d_H-i-s')),
                ]),
        ])
        ->defaultSort('recap_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyRecaps::route('/'),
            'create' => Pages\CreateDailyRecap::route('/create'),
            'edit' => Pages\EditDailyRecap::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->role === 'admin';
    }
}
