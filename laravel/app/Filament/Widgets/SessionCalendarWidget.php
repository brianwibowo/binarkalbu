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
                    ->formatStateUsing(function ($record) {
                        if (!$record) return '';
                        return \Carbon\Carbon::parse($record->session_start_time)->format('H:i')
                            . ' - ' .
                            \Carbon\Carbon::parse($record->session_end_time)->format('H:i');
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
            return [
                'id' => $session->id,
                'title' => "{$session->client?->name} ({$session->user?->name})",
                'start' => "{$session->session_date} {$session->session_start_time}",
                'end' => "{$session->session_date} {$session->session_end_time}",
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
