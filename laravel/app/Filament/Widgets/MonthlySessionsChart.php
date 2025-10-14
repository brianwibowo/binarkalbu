<?php

namespace App\Filament\Widgets;

use App\Models\ClientSession;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlySessionsChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Sesi Bulanan';
    protected static ?string $pollingInterval = null; // Matikan refresh otomatis

    // Properti untuk menerima data filter
    public ?string $year = null;
    public ?string $month = null;

    public static function canView(): bool
    {
        return false;
    }

    protected function getData(): array
    {
        // Tentukan tahun dan bulan yang akan digunakan
        $activeYear = $this->year ?? date('Y');

        // Mulai query dasar
        $query = ClientSession::query();

        // Terapkan filter jika user adalah psikolog
        if (Auth::user()->role === 'psikolog') {
            $query->where('user_id', Auth::id());
        }

        // Lanjutkan proses dengan data yang sudah terfilter
        $data = $query
            ->select(
                DB::raw('MONTH(session_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('session_date', $activeYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->all();

        $labels = [];
        $values = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->format('M');
            $labels[] = $monthName;
            $values[] = $data[$i] ?? 0;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Sesi',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}