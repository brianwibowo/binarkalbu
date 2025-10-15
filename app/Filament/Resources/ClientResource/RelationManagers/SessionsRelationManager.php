<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    public function form(Form $form): Form
    {
        $isAdmin = Auth::user()->role === 'admin';

        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Psikolog')
                    ->options(User::where('role', 'psikolog')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->hidden(! $isAdmin)
                    ->dehydrated(),
                DatePicker::make('session_date')
                    ->label('Tanggal Sesi')
                    ->required()
                    ->hidden(! $isAdmin)
                    ->dehydrated(),
                TimePicker::make('session_start_time')
                    ->label('Jam Mulai')
                    ->required()
                    ->seconds(false)
                    ->hidden(! $isAdmin)
                    ->dehydrated(),
                TimePicker::make('session_end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->seconds(false)
                    ->hidden(! $isAdmin)
                    ->dehydrated(),
                DatePicker::make('transfer_date')
                    ->label('Tanggal Transfer')
                    ->hidden(! $isAdmin),
                Select::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options(['dp' => 'DP', 'lunas' => 'Lunas'])
                    ->required()
                    ->hidden(! $isAdmin),
                TextInput::make('payment_amount')
                    ->label('Jumlah Bayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->hidden(! $isAdmin),
                Select::make('session_status')
                    ->label('Status Sesi')
                    ->options(['belum_terpakai' => 'Belum Terpakai', 'terpakai' => 'Terpakai'])
                    ->required()
                    ->hidden(! $isAdmin),

                Textarea::make('session_description')
                    ->label('Rekap/Hasil Sesi')
                    ->columnSpanFull(),

                FileUpload::make('medical_record_path')
                    ->label('Upload Dokumen RM (File PDF)')
                    ->disk('public')
                    ->directory('medical-records')
                    ->deletable(true)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        $isAdmin = Auth::user()->role === 'admin';

        return $table
            ->recordTitleAttribute('session_date')
            ->columns([
                TextColumn::make('session_date')->label('Tanggal Sesi')->date()->sortable(),
                TextColumn::make('session_status')
                    ->label('Status Sesi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_terpakai' => 'warning',
                        'terpakai' => 'success',
                    }),
                TextColumn::make('user.name')->label('Psikolog'),
                TextColumn::make('session_description')
                    ->label('Rekap Sesi')
                    ->limit(50)
                    ->tooltip('Klik untuk melihat rekap lengkap pada mode Edit'),
                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->hidden(! $isAdmin)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dp' => 'gray',
                        'lunas' => 'primary',
                    }),
            ])
            ->filters([
                SelectFilter::make('session_status')
                    ->label('Status Sesi')
                    ->options(['belum_terpakai' => 'Belum Terpakai', 'terpakai' => 'Terpakai']),
                SelectFilter::make('payment_status')
                    ->hidden(! $isAdmin)
                    ->label('Status Pembayaran')
                    ->options(['dp' => 'DP', 'lunas' => 'Lunas']),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible(fn (): bool => $isAdmin),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Tombol download file public
                Action::make('download_medical_record')
                    ->label('Unduh RM')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {
                        if ($record->medical_record_path && Storage::disk('public')->exists($record->medical_record_path)) {
                            return response()->download(
                                Storage::disk('public')->path($record->medical_record_path)
                            );
                        }
                    }),

                Tables\Actions\DeleteAction::make()->visible(fn (): bool => $isAdmin),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn (): bool => $isAdmin),
                ]),
            ]);
    }
}
