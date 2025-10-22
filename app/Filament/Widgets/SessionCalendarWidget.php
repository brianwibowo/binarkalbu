<?php
namespace App\Filament\Widgets;

use App\Models\ClientSession;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class SessionCalendarWidget extends FullCalendarWidget
{
    protected int | string | array $columnSpan = 'full';
    
    public Model|int|string|null $record = null;

    /**
     * Konfigurasi FullCalendar
     */
    public function config(): array
    {
        return [
            'displayEventTime' => false, // Sembunyikan waktu otomatis
            'locale' => 'id', // Set locale Indonesia
        ];
    }

    /**
     * Menonaktifkan tombol "New event" di header kalender
     */
    protected function headerActions(): array
    {
        return [];
    }

    /**
     * Event listener saat jadwal diklik
     */
    public function onEventClick(array $event): void
    {
        $this->record = ClientSession::with(['client', 'user'])->find($event['id']);
        $this->mountAction('view');
    }

    /**
     * Definisikan actions yang tersedia
     */
    protected function modalActions(): array
    {
        return [
            $this->viewAction(),
        ];
    }

    /**
     * Action untuk melihat detail sesi
     */
    protected function viewAction(): Action
    {
        return Action::make('view')
            ->label('Detail Sesi')
            ->modalHeading('Detail Sesi Konseling')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->infolist(fn (Infolist $infolist) => $infolist
                ->record($this->record)
                ->schema([
                    TextEntry::make('client.client_code')
                        ->label('Kode Klien'),
                    
                    TextEntry::make('client.name')
                        ->label('Nama Klien'),
                    
                    TextEntry::make('user.name')
                        ->label('Psikolog'),
                    
                    TextEntry::make('session_date')
                        ->label('Tanggal Sesi')
                        ->date('d F Y'),
                    
                    TextEntry::make('session_start_time')
                        ->label('Waktu Sesi')
                        ->formatStateUsing(function ($state, $record) {
                            if (!$record) return '-';
                            return \Carbon\Carbon::parse($record->session_start_time)->format('H:i')
                                . ' - ' .
                                \Carbon\Carbon::parse($record->session_end_time)->format('H:i');
                        }),
                    
                    TextEntry::make('session_description')
                        ->label('Rekap/Hasil Sesi')
                        ->columnSpanFull()
                        ->default('-')
                        ->html(),
                ])
            );
    }

    /**
     * Ambil data event dari database untuk kalender
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
            $startTime = \Carbon\Carbon::parse($session->session_start_time)->format('H:i');
            $endTime = \Carbon\Carbon::parse($session->session_end_time)->format('H:i');
            
            $clientName = $session->client?->name ?? 'Klien (Dihapus)';
            $clientCode = $session->client?->client_code ?? '-';
            
            return [
                'id' => $session->id,
                'title' => "$startTime - $endTime [$clientCode] $clientName",
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