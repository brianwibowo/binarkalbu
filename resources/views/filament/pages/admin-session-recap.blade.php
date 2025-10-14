<x-filament-panels::page>
    {{-- Bagian Filter --}}
    <x-filament::section>
        <h2 class="text-xl font-bold tracking-tight">Filter Data</h2>
        <div class="flex items-end mt-4 space-x-4">
            <div class="flex-1">
                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tahun:</label>
                <x-filament::input.select wire:model.live="year" id="year" class="mt-1">
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </x-filament::input.select>
            </div>
            <div class="flex-1">
                <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Bulan:</label>
                <x-filament::input.select wire:model.live="month" id="month" class="mt-1">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </x-filament::input.select>
            </div>
            <div>
                <x-filament::button wire:click="applyFilters">
                    Terapkan
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    {{-- Bagian Card Statistik --}}
    <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-3">
        <x-filament::section class="text-center">
            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Sesi Hari Ini</h3>
            <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">{{ $sessionsToday }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Sesi di Bulan {{ $months[(int)$month] ?? '' }}</h3>
            <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">{{ $sessionsInFilter }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Total Semua Sesi</h3>
            <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">{{ $totalSessions }}</p>
        </x-filament::section>
    </div>
    
    {{-- Bagian Ranking & Grafik --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        {{-- Kolom Ranking --}}
        <div class="lg:col-span-1">
            <x-filament::section>
                <h2 class="text-xl font-bold tracking-tight">Psikolog Teraktif (Bulan {{ $months[(int)$month] ?? '' }})</h2>
                <div class="mt-4 space-y-4">
                    @forelse($topPsychologists as $psychologist)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-gray-800">
                            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $loop->iteration }}. {{ $psychologist->name }}</span>
                            <span class="font-bold text-primary-600 dark:text-primary-400">{{ $psychologist->session_count }} Sesi</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500">Tidak ada data sesi di periode ini.</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        {{-- Kolom Grafik --}}
        <div class="lg:col-span-2">
            @livewire(\App\Filament\Widgets\MonthlySessionsChart::class)
        </div>
    </div>
</x-filament-panels::page>