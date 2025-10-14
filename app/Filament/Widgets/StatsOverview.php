<?php

namespace App\Filament\Widgets;

use App\Models\ClientSession;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
    $today = Carbon::today();

    // Mulai query dasar
    $query = ClientSession::query();

    // JIKA yang login adalah psikolog, tambahkan filter
    if (Auth::user()?->role === 'psikolog') {
    $query->where('user_id', Auth::id());
    }


    // Lanjutkan perhitungan dengan data yang sudah terfilter
    $sessionsToday = (clone $query)->whereDate('session_date', $today)->count();
    $upcomingSessions = (clone $query)->whereDate('session_date', '>', $today)->count();

    return [
        Stat::make('Sesi Hari Ini', $sessionsToday)
            ->description('Jumlah sesi yang dijadwalkan hari ini')
            ->color('success'),
        Stat::make('Sesi Mendatang', $upcomingSessions)
            ->description('Jumlah sesi setelah hari ini')
            ->color('primary'),
    ];
    }
    protected static ?int $sort = 2;
}