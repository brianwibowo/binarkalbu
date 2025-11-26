<?php

namespace App\Filament\Widgets;

use App\Models\ClientSession;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class SessionCalendarWidget extends FullCalendarWidget
{
    protected int | string | array $columnSpan = 'full';

    /**
     * Menonaktifkan tombol “New Session” di header kalender
     */
    protected function headerActions(): array
    {
        return [];
    }

    /**
     * Menonaktifkan tombol Edit/Delete pada popup event
     */
    protected function getModalActions(): array
    {
        return [
            $this->getViewAction(),
        ];
    }

    /**
     * Aksi “View” untuk menampilkan detail sesi (tanpa edit/delete)
     */
    protected function getViewAction(): Action
    {
        return ViewAction::make()
            ->infolist([
                TextEntry::make('client.name')->label('Nama Klien'),
                TextEntry::make('user.name')->label('Psikolog'),
                TextEntry::make('session_date')->label('Tanggal Sesi')
                    ->date('d F Y'),
                TextEntry::make('session_start_time')->label('Waktu')
                    ->state(function ($record) {
                        if (!$record) return '-';
                        $startTime = $record->session_start_time ?? '00:00';
                        $endTime = $record->session_end_time ?? '00:00';
                        // Ensure format is H:i (remove seconds if present)
                        $startTime = substr($startTime, 0, 5);
                        $endTime = substr($endTime, 0, 5);
                        return "$startTime - $endTime";
                    }),
                TextEntry::make('session_description')->label('Rekap/Hasil Sesi')->columnSpanFull(),
            ]);
    }

    /**
     * Ambil data event dari database untuk kalender
     */
    public function fetchEvents(array $fetchInfo): array
    {
        /** @var User $user */
        $user = Auth::user();

        $query = ClientSession::query()
            ->with(['client', 'user'])
            ->whereBetween('session_date', [$fetchInfo['start'], $fetchInfo['end']]);

        if ($user?->role === 'psikolog') {
            $query->where('user_id', $user->id);
        }

        return $query->get()->map(function (ClientSession $session) {
            // Handle time fields properly - they're stored as TIME type (H:i:s)
            $startTime = $session->session_start_time ?? '00:00';
            $endTime = $session->session_end_time ?? '00:00';
            
            // Ensure format is H:i (remove seconds if present)
            $startTime = substr($startTime, 0, 5);
            $endTime = substr($endTime, 0, 5);
            
            $clientName = $session->client?->name ?? 'Klien (Dihapus)';
            $userName = $session->user?->name ?? 'Psikolog (Dihapus)';
            
            return [
                'id' => $session->id,
                'title' => "$startTime - $endTime $clientName ($userName)",
                'start' => "{$session->session_date}T{$session->session_start_time}",
                'end' => "{$session->session_date}T{$session->session_end_time}",
            ];
        })->all();
    }

    /**
     * Pastikan tidak ada tombol Create sama sekali
     */
    public static function canCreate(): bool
    {
        return false;
    }
    protected static ?int $sort = 3;
}
