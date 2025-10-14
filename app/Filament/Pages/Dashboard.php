<?php

namespace App\Filament\Pages;

use App\Filament\Widgets;
use Filament\Pages\Dashboard as BasePage;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BasePage
{
    /**
     * Tampilkan widget yang sama untuk semua role.
     * Urutan di sini menentukan urutan di layar.
     */
    public function getWidgets(): array
    {
        return [
            Widgets\StatsOverview::class,       // Statistik akan muncul di atas
            Widgets\SessionCalendarWidget::class, // Kalender akan muncul di bawahnya
        ];
    }

    /**
     * Gunakan layout 1 kolom yang sama untuk semua role.
     */
    public function getColumns(): int | string | array
    {
        return 1;
    }
    
    /**
     * Pastikan tidak ada widget header bawaan yang muncul.
     */
    protected function getHeaderWidgets(): array
    {
        return [];
    }
}