<?php

namespace App\Filament\Exports;

use App\Models\Client;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class ClientWithSessionsExport extends ExcelExport
{
    protected string $name = 'Clients with Sessions';

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        $query = Client::query()->with('sessions.user');

        if (Auth::user()->role === 'psikolog') {
            $query->whereHas('sessions', fn($q) => $q->where('user_id', Auth::id()));
        }

        return $query;
    }

    public function columns(): array
    {
        $isAdmin = Auth::user()->role === 'admin';

        $columns = [
            TextColumn::make('name')->label('Nama Klien'),
            TextColumn::make('date_of_birth')->label('Tanggal Lahir'),
            TextColumn::make('sessions.session_date')->label('Tanggal Sesi'),
            TextColumn::make('sessions.session_status')->label('Status Sesi'),
            TextColumn::make('sessions.user.name')->label('Nama Psikolog'),
            TextColumn::make('sessions.session_description')->label('Rekap Sesi'),
        ];

        if ($isAdmin) {
            array_splice($columns, 2, 0,
                [
                    TextColumn::make('whatsapp_number')->label('No. WhatsApp'),
                    TextColumn::make('address')->label('Alamat'),
                    TextColumn::make('initial_diagnosis')->label('Diagnosa Awal'),
                ]
            );
        }

        return $columns;
    }

    public function filename(): string
    {
        return 'clients_with_sessions.xlsx';
    }
}
