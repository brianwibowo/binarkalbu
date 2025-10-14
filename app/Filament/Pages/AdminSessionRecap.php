<?php

namespace App\Filament\Pages;

use App\Models\ClientSession;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSessionRecap extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string $view = 'filament.pages.admin-session-recap';
    protected static ?string $title = 'Rekap Sesi Global';
    protected static ?string $navigationLabel = 'Rekap Sesi Global';
    protected static ?int $navigationSort = 4;

    // Properti untuk filter
    public $year;
    public $month;
    public $years = [];
    public $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // Properti untuk statistik
    public $sessionsToday = 0;
    public $sessionsInFilter = 0;
    public $totalSessions = 0;
    public $topPsychologists = [];

    // Hanya tampilkan halaman ini untuk Admin
    public static function canAccess(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public function mount(): void
    {
        $this->year = Carbon::now()->year;
        $this->month = Carbon::now()->month;
        $this->years = range(Carbon::now()->year, 2020);
        $this->applyFilters();
    }

    public function applyFilters(): void
    {
        $today = Carbon::today();

        // Hitung statistik total (tidak terpengaruh role)
        $this->sessionsToday = ClientSession::whereDate('session_date', $today)->count();
        $this->sessionsInFilter = ClientSession::whereYear('session_date', $this->year)->whereMonth('session_date', $this->month)->count();
        $this->totalSessions = ClientSession::count();

        // Hitung ranking psikolog berdasarkan filter bulan & tahun
        $this->topPsychologists = DB::table('client_sessions')
            ->join('users', 'users.id', '=', 'client_sessions.user_id')
            ->select('users.name', DB::raw('count(client_sessions.id) as session_count'))
            ->where('users.role', 'psikolog')
            ->whereYear('client_sessions.session_date', $this->year)
            ->whereMonth('client_sessions.session_date', $this->month)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('session_count')
            ->limit(5) // Ambil 5 teratas
            ->get();

        // Kirim event untuk me-refresh grafik dengan filter baru
        $this->dispatch('updateChart', year: $this->year, month: $this->month);
    }
}