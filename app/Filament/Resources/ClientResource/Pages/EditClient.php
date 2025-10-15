<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    /**
     * Menyembunyikan tombol aksi di header (pojok kanan atas) untuk psikolog.
     */
    protected function getHeaderActions(): array
    {
        if (Auth::user()->role === 'psikolog') {
            return [];
        }

        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * LOGIKA BARU: Menyembunyikan tombol aksi di footer (Save & Cancel) untuk psikolog.
     */
    protected function getFormActions(): array
    {
        // Jika user yang login adalah psikolog, jangan tampilkan tombol apapun di bawah.
        if (Auth::user()->role === 'psikolog') {
            return [];
        }

        // Jika admin, tampilkan tombol default (Save Changes & Cancel).
        return parent::getFormActions();
    }
}