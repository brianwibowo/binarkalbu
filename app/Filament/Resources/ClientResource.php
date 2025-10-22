<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action; // ✅ Tambahkan ini
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel; // ✅ Tambahkan ini
use App\Filament\Exports\ClientWithSessionsExport; // ✅ Tambahkan ini (pastikan file-nya ada di App/Exports)

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        $isAdmin = Auth::user()->role === 'admin';

        return $form
            ->schema([
                Group::make()
                    ->schema([
                        TextInput::make('client_code')
                            ->label('Kode Klien')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir'),
                        TextInput::make('whatsapp_number')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->maxLength(20),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Textarea::make('initial_diagnosis')
                            ->label('Diagnosis Awal')
                            ->columnSpanFull(),
                    ])
                    ->hidden(!$isAdmin),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isPsikolog = Auth::user()->role === 'psikolog';

        return $table
            ->columns([
                TextColumn::make('client_code')
                    ->label('Kode Klien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable(),
                TextColumn::make('whatsapp_number')
                    ->label('Nomor WhatsApp')
                    ->searchable()
                    ->hidden($isPsikolog),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden($isPsikolog),

                Action::make('exportExcel')
                    ->label('Export ke Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $fileName = 'Data Klien & Sesi - ' . now()->format('Y-m-d_H-i-s') . '.xlsx';

                        return Excel::download(
                            new ClientWithSessionsExport(),
                            $fileName
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden($isPsikolog),
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
