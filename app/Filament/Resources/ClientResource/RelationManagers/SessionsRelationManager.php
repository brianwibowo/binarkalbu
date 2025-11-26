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
use Filament\Notifications\Notification; // Untuk notifikasi error
use Filament\Infolists\Components\TextEntry; // Untuk View Modal
use Filament\Infolists\Components\ImageEntry; // Untuk Preview Gambar
use Filament\Infolists\Components\Section; // Untuk Layout View
use Filament\Infolists\Components\View; // Untuk custom view
use ZipArchive;

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
                    ->hidden(! $isAdmin)
                    ->dehydrated(),
                TimePicker::make('session_start_time')
                    ->label('Jam Mulai')
                    ->seconds(false)
                    ->hidden(! $isAdmin)
                    ->dehydrated(),
                TimePicker::make('session_end_time')
                    ->label('Jam Selesai')
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

                // Info existing files
                \Filament\Forms\Components\Placeholder::make('files_note')
                    ->content(function ($record) {
                        if (!$record || empty($record->medical_record_path)) {
                            return 'Tidak ada file';
                        }
                        
                        $files = is_array($record->medical_record_path) ? $record->medical_record_path : [$record->medical_record_path];
                        $files = array_filter($files, fn($f) => !empty($f));
                        
                        if (empty($files)) return 'Tidak ada file';
                        return 'File yang ada: ' . count($files) . ' file (lihat di tombol "Lihat" untuk preview)';
                    })
                    ->visible(function ($record) {
                        return $record && !empty($record->medical_record_path);
                    })
                    ->columnSpanFull(),

                FileUpload::make('medical_record_path')
                    ->label('Upload Dokumen RM (Bisa Banyak)')
                    ->disk('public')
                    ->directory('medical-records')
                    ->multiple()
                    ->reorderable()
                    ->openable()
                    ->downloadable(false)
                    ->deletable(true)
                    ->maxSize(50 * 1024)
                    ->acceptedFileTypes(['image/*', 'application/pdf', '.xlsx', '.xls', '.doc', '.docx'])
                    ->dehydrated(true)
                    ->previewable(false)
                    ->visibility('public')
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
                TextColumn::make('session_start_time')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('session_end_time')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),
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
                
                // Indikator file (menampilkan jumlah file)
                TextColumn::make('medical_record_path')
                    ->label('File RM')
                    ->getStateUsing(function ($record) {
                        // Direct access dari record untuk ensure data selalu fresh
                        $value = $record->medical_record_path;
                        if (empty($value)) return '-';
                        
                        $files = is_array($value) ? $value : [$value];
                        $validFiles = array_filter($files, fn($f) => !empty($f) && is_string($f));
                        $count = count($validFiles);
                        
                        return $count > 0 ? $count . ' File' : '-';
                    })
                    ->badge()
                    ->color('info'),

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
                // --- AKSI VIEW (BARU) ---
                Tables\Actions\ViewAction::make()
                    ->label('Lihat')
                    ->color('info')
                    ->infolist(fn (\Filament\Infolists\Infolist $infolist) => $infolist
                        ->schema([
                            Section::make('Detail Sesi')
                                ->schema([
                                    TextEntry::make('session_date')->label('Tanggal')->date(),
                                    TextEntry::make('user.name')->label('Psikolog'),
                                    TextEntry::make('session_description')->label('Rekap Sesi')->columnSpanFull(),
                                ])->columns(2),

                            Section::make('Dokumen Rekam Medis')
                                ->schema([
                                    TextEntry::make('medical_record_path')
                                        ->label('Preview & Daftar File')
                                        ->state(function ($record) {
                                            // Force refresh dari database record
                                            $files = $record->medical_record_path ?? [];
                                            if (!is_array($files)) {
                                                $files = [$files];
                                            }
                                            $files = array_filter($files, fn($f) => !empty($f));
                                            
                                            if (empty($files)) {
                                                return '<p class="text-gray-500">Tidak ada file</p>';
                                            }
                                            
                                            $html = '<div class="space-y-4">';
                                            
                                            // 1. Preview gambar
                                            $imageFiles = array_filter($files, function($file) {
                                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            });
                                            
                                            if (!empty($imageFiles)) {
                                                $html .= '<div class="grid grid-cols-3 gap-2 mb-4">';
                                                foreach (array_slice($imageFiles, 0, 6) as $file) {
                                                    // Use relative URL agar work di semua domain
                                                    $url = '/storage/' . $file;
                                                    $name = basename($file);
                                                    $html .= "<img src=\"{$url}\" alt=\"{$name}\" class=\"w-full h-24 rounded-lg border border-gray-200 object-cover\" onerror=\"this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3C/svg%3E'\" />";
                                                }
                                                $html .= '</div>';
                                            }
                                            
                                            // 2. Daftar file
                                            $html .= '<div class="space-y-1 text-sm">';
                                            $html .= '<p class="font-bold">📋 File (' . count($files) . ')</p>';
                                            foreach ($files as $file) {
                                                // Use relative URL
                                                $url = '/storage/' . $file;
                                                $name = basename($file);
                                                $html .= "<a href=\"{$url}\" target=\"_blank\" class=\"text-blue-600 hover:underline block\">📎 {$name}</a>";
                                            }
                                            $html .= '</div>';
                                            $html .= '</div>';
                                            
                                            return $html;
                                        })
                                        ->html()
                                        ->columnSpanFull(),
                                ])
                        ])
                    ),

                Tables\Actions\EditAction::make(),

                // --- AKSI DOWNLOAD (DIPERBAIKI) ---
                Action::make('download_medical_record')
                    ->label('Unduh RM')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {
                        if (!$record->medical_record_path) {
                            Notification::make()->title('Tidak ada file untuk diunduh')->warning()->send();
                            return;
                        }

                        $files = is_array($record->medical_record_path) 
                            ? $record->medical_record_path 
                            : [$record->medical_record_path];

                        // Filter file valid
                        $validFiles = array_filter($files, function($path) {
                            return Storage::disk('public')->exists($path);
                        });

                        if (empty($validFiles)) {
                            Notification::make()->title('File fisik tidak ditemukan di server')->danger()->send();
                            return;
                        }

                        // Skenario 1: Single File
                        if (count($validFiles) === 1) {
                            $path = reset($validFiles);
                            return response()->download(Storage::disk('public')->path($path));
                        }

                        // Skenario 2: Multiple Files (ZIP)
                        $zipFileName = 'RM-' . $record->id . '-' . time() . '.zip';
                        // Gunakan public_path() karena disk public kamu mengarah kesana
                        $zipPath = public_path('storage/' . $zipFileName);

                        $zip = new ZipArchive;
                        // Cek apakah bisa membuat file ZIP
                        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                            foreach ($validFiles as $file) {
                                // Ambil path absolut file sumber
                                $absolutePath = Storage::disk('public')->path($file);
                                $zip->addFile($absolutePath, basename($file));
                            }
                            $zip->close();
                            
                            return response()->download($zipPath)->deleteFileAfterSend(true);
                        } else {
                            Notification::make()->title('Gagal membuat file ZIP (Izin Folder?)')->danger()->send();
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