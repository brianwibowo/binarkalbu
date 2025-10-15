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
    
    // Properti ini sudah memiliki tipe data yang benar untuk menghindari error.
    public Model|int|string|null $record = null;

    /**
     * Menonaktifkan tombol "New event" di header kalender
     */
    protected function headerActions(): array
    {
        return [];
    }

    /**
     * KUNCI #1: Ini adalah event listener yang dijalankan saat jadwal diklik.
     * Tugasnya adalah mengambil data dari DB dan menyiapkan Aksi 'view'.
     */
    public function onEventClick(array $event): void
    {
        // 1. Ambil record dari database menggunakan ID dari event yang diklik
        $this->record = ClientSession::find($event['id']);

        // 2. Panggil (mount) Aksi 'view'. Ini akan memicu pop-up.
        $this->mountAction('view');
    }

    /**
     * KUNCI #2: Kita KEMBALI menggunakan getModalActions()
     * Ini adalah fungsi yang dipanggil oleh kalender untuk membuat tombol di dalam pop-up.
     */
    protected function getModalActions(): array
    {
        return [
            // Hanya tampilkan tombol "View"
            $this->getViewAction(),
        ];
    }
    
    /**
     * KUNCI #3: Kita KEMBALI menggunakan getViewAction() dengan ViewAction dari package.
     * Kode ini sekarang akan bekerja karena onEventClick sudah menyiapkan datanya.
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
     * Ambil data event dari database untuk kalender (Kode ini sudah benar)
     */
    public function fetchEvents(array $fetchInfo): array
    {
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
                'title' => $session->client?->name ?? 'Klien (Dihapus)',
                'start' => "{$session->session_date} {$session->session_start_time}",
                'end' => "{$session->session_date} {$session->session_end_time}",
            ];
        })->all();
    }
    
    public static function canCreate(): bool
    {
        return false;
    }

    protected static ?int $sort = 3;
}