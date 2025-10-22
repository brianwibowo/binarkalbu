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
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // Properti untuk statistik
    public $sessionsToday = 0;
    public $sessionsInMonth = 0;
    public $sessionsInYear = 0;
    public $totalSessions = 0;
    public $topPsychologistsMonth = [];
    public $topPsychologistsYear = [];

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
        $this->loadStatistics();
    }

    // Method ini dipanggil otomatis saat year atau month berubah
    public function updatedYear(): void
    {
        $this->loadStatistics();
    }

    public function updatedMonth(): void
    {
        $this->loadStatistics();
    }

    public function loadStatistics(): void
    {
        $today = Carbon::today();

        // Hitung statistik
        $this->sessionsToday = ClientSession::whereDate('session_date', $today)->count();
        
        $this->sessionsInMonth = ClientSession::whereYear('session_date', $this->year)
            ->whereMonth('session_date', $this->month)
            ->count();
        
        $this->sessionsInYear = ClientSession::whereYear('session_date', $this->year)
            ->count();
        
        $this->totalSessions = ClientSession::count();

        // Ranking psikolog berdasarkan BULAN yang dipilih
        $this->topPsychologistsMonth = ClientSession::query()
            ->join('users', 'users.id', '=', 'client_sessions.user_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(client_sessions.id) as session_count')
            )
            ->where('users.role', 'psikolog')
            ->whereYear('client_sessions.session_date', $this->year)
            ->whereMonth('client_sessions.session_date', $this->month)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('session_count')
            ->limit(5)
            ->get();

        // Ranking psikolog berdasarkan TAHUN yang dipilih
        $this->topPsychologistsYear = ClientSession::query()
            ->join('users', 'users.id', '=', 'client_sessions.user_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(client_sessions.id) as session_count')
            )
            ->where('users.role', 'psikolog')
            ->whereYear('client_sessions.session_date', $this->year)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('session_count')
            ->limit(5)
            ->get();

        // Kirim event untuk update chart
        $this->dispatch('updateChart', year: $this->year, month: $this->month);
    }
}