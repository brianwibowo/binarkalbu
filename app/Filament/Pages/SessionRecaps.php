<?php

namespace App\Filament\Pages;

use App\Models\ClientSession;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SessionRecaps extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string $view = 'filament.pages.session-recaps';
    protected static ?string $title = 'Rekap Sesi';
    protected static ?string $navigationLabel = 'Rekap Sesi';
    protected static ?int $navigationSort = 3; // Urutan di sidebar

    // Properti untuk menyimpan nilai filter
    public $year;
    public $month;
    public $years = [];
    public $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // Properti untuk menyimpan data statistik
    public $sessionsToday = 0;
    public $sessionsThisMonth = 0;
    public $totalSessions = 0;

    // Hanya tampilkan halaman ini untuk psikolog
    public static function canAccess(): bool
    {
        return Auth::user()->role === 'psikolog';
    }

    public function mount(): void
    {
        // Inisialisasi filter dengan bulan dan tahun saat ini
        $this->year = Carbon::now()->year;
        $this->month = Carbon::now()->month;
        $this->years = range(Carbon::now()->year, 2020); // Daftar tahun dari sekarang s.d. 2020
        $this->applyFilters();
    }

    public function applyFilters(): void
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Hitung Sesi Hari Ini
        $this->sessionsToday = ClientSession::where('user_id', $user->id)
            ->whereDate('session_date', $today)
            ->count();
            
        // Hitung Sesi Bulan Ini (berdasarkan filter)
        $this->sessionsThisMonth = ClientSession::where('user_id', $user->id)
            ->whereYear('session_date', $this->year)
            ->whereMonth('session_date', $this->month)
            ->count();

        // Hitung Total Sesi (sepanjang masa)
        $this->totalSessions = ClientSession::where('user_id', $user->id)->count();
    }
}