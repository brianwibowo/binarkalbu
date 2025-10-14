<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    public function form(Form $form): Form
    {
        $isPsychologist = Auth::user()->role === 'psikolog';

        return $form
            ->schema([
                // HANYA FIELD INI YANG BISA DIEDIT PSIKOLOG
                Textarea::make('session_description')->label('Rekap/Hasil Sesi')->columnSpanFull(),
                FileUpload::make('medical_record_path')
                    ->label('Upload Dokumen RM (Jika Ada)')
                    ->directory('medical-records')
                    ->visibility('public')
                    ->disk('public') 
                    ->downloadable()
                    ->openable()
                    ->deletable(true)
                    ->columnSpanFull(),

                Select::make('user_id')->label('Psikolog')->options(User::where('role', 'psikolog')->pluck('name', 'id'))->searchable()->required()->disabled()->dehydrated(),
                DatePicker::make('session_date')->label('Tanggal Sesi')->required()->disabled()->dehydrated(),
                TimePicker::make('session_start_time')->label('Jam Mulai')->required()->disabled()->dehydrated(),
                TimePicker::make('session_end_time')->label('Jam Selesai')->required()->disabled()->dehydrated(),
                DatePicker::make('transfer_date')->label('Tanggal Transfer')->disabled($isPsychologist),
                Select::make('payment_status')->label('Status Pembayaran')->options(['dp' => 'DP', 'lunas' => 'Lunas'])->required()->disabled($isPsychologist),
                TextInput::make('payment_amount')->label('Jumlah Bayar')->numeric()->prefix('Rp')->disabled($isPsychologist),
                Select::make('session_status')->label('Status Sesi')->options(['belum_terpakai' => 'Belum Terpakai', 'terpakai' => 'Terpakai'])->required()->disabled($isPsychologist),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('session_date')
            ->columns([
                TextColumn::make('session_date')->label('Tanggal Sesi')->date()->sortable(),
                TextColumn::make('session_status')->badge()->color(fn (string $state): string => match ($state) {
                    'belum_terpakai' => 'warning',
                    'terpakai' => 'success',
                }),
                TextColumn::make('payment_status')->badge()->color(fn (string $state): string => match ($state) {
                    'dp' => 'gray',
                    'lunas' => 'primary',
                }),
                TextColumn::make('user.name')->label('Psikolog'),
            ])
            ->filters([
                SelectFilter::make('session_status')->label('Status Sesi')->options(['belum_terpakai' => 'Belum Terpakai', 'terpakai' => 'Terpakai']),
                SelectFilter::make('payment_status')->label('Status Pembayaran')->options(['dp' => 'DP', 'lunas' => 'Lunas']),
            ])
            ->headerActions([
                // Sembunyikan tombol 'Create' dari psikolog
                Tables\Actions\CreateAction::make()->visible(fn (): bool => Auth::user()->role === 'admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Sembunyikan tombol 'Delete' dari psikolog
                Tables\Actions\DeleteAction::make()->visible(fn (): bool => Auth::user()->role === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn (): bool => Auth::user()->role === 'admin'),
                ]),
            ]);
    }
}