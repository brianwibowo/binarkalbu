<?php

namespace App\Filament\Widgets;

use App\Models\ClientSession;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Carbon\Carbon;

class SessionCalendarWidget extends FullCalendarWidget
{
    protected int | string | array $columnSpan = 'full';
    
    public Model|int|string|null $record = null;

    public function config(): array
    {
        return [
            'locale' => 'id',
            'timeZone' => 'Asia/Jakarta',
            'nowIndicator' => true,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            ],
        ];
    }

    protected function headerActions(): array
    {
        return [];
    }

    public function onEventClick(array $event): void
    {
        $this->record = ClientSession::with(['client', 'user'])->find($event['id']);
        if ($this->record) {
            $this->mountAction('view');
        }
    }

    protected function modalActions(): array
    {
        return [$this->viewAction()];
    }

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
                    TextEntry::make('client.client_code')->label('Kode Klien'),
                    TextEntry::make('client.name')->label('Nama Klien'),
                    TextEntry::make('user.name')->label('Psikolog'),
                    TextEntry::make('session_date')->label('Tanggal Sesi')->date('d F Y'),
                    TextEntry::make('session_start_time')
                        ->label('Waktu Sesi')
                        ->formatStateUsing(function ($state, $record) {
                            if (!$record || !$record->session_start_time || !$record->session_end_time) return '-';
                            try {
                                $start = Carbon::parse($record->session_start_time)->format('H:i');
                                $end = Carbon::parse($record->session_end_time)->format('H:i');
                                return "$start - $end";
                            } catch (\Exception $e) {
                                return "$record->session_start_time - $record->session_end_time";
                            }
                        }),
                    TextEntry::make('session_description')
                        ->label('Rekap/Hasil Sesi')
                        ->columnSpanFull()
                        ->default('-')
                        ->html(),
                ])
            );
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $user = Auth::user();
        
        $startDate = Carbon::parse($fetchInfo['start'])->startOfDay();
        $endDate = Carbon::parse($fetchInfo['end'])->endOfDay();
        
        $query = ClientSession::query()
            ->with(['client', 'user'])
            ->whereNotNull('session_date')
            ->whereNotNull('session_start_time')
            ->whereNotNull('session_end_time')
            ->whereBetween('session_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if ($user && $user->role === 'psikolog') {
            $query->where('user_id', $user->id);
        }

        return $query->get()
            ->map(function (ClientSession $session) {
                try {
                    // Ambil Tanggal saja (Format Y-m-d)
                    $dateOnly = $session->session_date instanceof \Carbon\Carbon 
                        ? $session->session_date->format('Y-m-d') 
                        : substr((string)$session->session_date, 0, 10);

                    // Gabungkan Tanggal + Jam
                    $startDateTime = Carbon::parse("$dateOnly {$session->session_start_time}");
                    $endDateTime = Carbon::parse("$dateOnly {$session->session_end_time}");
                    
                    // Format ISO String untuk FullCalendar
                    $startIso = $startDateTime->toDateTimeString();
                    $endIso = $endDateTime->toDateTimeString();

                } catch (\Exception $e) {
                    $startIso = $session->session_date; 
                    $endIso = $session->session_date;
                }

                $clientName = $session->client?->name ?? 'Klien (Dihapus)';
                $clientCode = $session->client?->client_code ?? '-';
                
                $color = match ($session->session_status) {
                    'terpakai' => '#10b981', // Hijau
                    'belum_terpakai' => '#f59e0b', // Kuning/Oranye
                    default => '#3b82f6', // Biru
                };

                return [
                    'id' => $session->id,
                    // PERUBAHAN DISINI: Hapus variabel jam ($startTimeFmt) dari title
                    'title' => "[$clientCode] $clientName", 
                    'start' => $startIso,
                    'end' => $endIso,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                ];
            })
            ->values()
            ->toArray();
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
    
    protected static ?int $sort = 3;
}