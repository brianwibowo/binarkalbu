<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Forms\Components\Group; // <-- PASTIKAN USE STATEMENT INI ADA

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        // Cek apakah user yang sedang login adalah Admin.
        $isAdmin = Auth::user()->role === 'admin';

        return $form
            ->schema([
                // BUNGKUS SEMUA FIELD DALAM SEBUAH GROUP
                // GROUP INI AKAN DISEMBUNYIKAN JIKA USER BUKAN ADMIN
                Group::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('date_of_birth'),
                        TextInput::make('whatsapp_number')
                            ->tel()
                            ->maxLength(20),
                        Textarea::make('address')
                            ->columnSpanFull(),
                        Textarea::make('initial_diagnosis')
                            ->columnSpanFull(),
                    ])
                    ->hidden(! $isAdmin) // <-- LOGIKA KUNCI: SEMBUNYIKAN DARI PSIKOLOG
            ]);
    }

    public static function table(Table $table): Table
    {
        // Cek apakah user adalah psikolog
        $isPsikolog = Auth::user()->role === 'psikolog';

        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                TextColumn::make('whatsapp_number')
                    ->searchable()
                    ->hidden($isPsikolog), // Kolom ini tetap disembunyikan dari tabel
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->hidden($isPsikolog), // Tombol Buat Klien Baru disembunyikan
                ExportAction::make('exportExcel')
                    ->exports([
                        ExcelExport::make('all_data')
                            ->fromTable()
                            ->withFilename('Data Klien -' . now()->format('Y-m-d_H-i-s'))
                    ]),
            ])
            ->actions([
                // Tombol Edit di baris ini SEKARANG TERLIHAT oleh semua role
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->hidden($isPsikolog),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SessionsRelationManager::class,
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->role === 'admin') {
            return $query;
        }

        return $query->whereHas('sessions', function (Builder $query) {
            $query->where('user_id', Auth::id());
        });
    }
}