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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(), // Menambahkan ikon pencarian
                TextColumn::make('whatsapp_number')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(), // Menambahkan fitur sort
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Kolom ini bisa disembunyikan
            ])
            ->filters([
                // Filter akan kita tambahkan nanti
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                ExportAction::make('exportExcel')
                    ->label('Export Excel')
                    ->exports([
                        ExcelExport::make('all_data')
                            ->fromTable()
                            ->withFilename('Data Klien -' . now()->format('Y-m-d_H-i-s'))
                    ]),
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
    // Ambil query dasar
    $query = parent::getEloquentQuery();

    // Jika user yang login adalah admin, tampilkan semua data
    if (Auth::user()->role === 'admin') {
        return $query;
    }

    // Jika psikolog, filter klien yang punya sesi dengan ID psikolog ini
    return $query->whereHas('sessions', function (Builder $query) {
        $query->where('user_id', Auth::id());
    });
    }
}
