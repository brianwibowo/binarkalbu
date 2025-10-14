<x-filament-panels::page>
    {{-- 🔍 Filter Section --}}
    <div class="p-6 mb-8 bg-white rounded-2xl shadow-sm dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-100">Filter Data Rekap</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            {{-- Tahun --}}
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                <select id="year" wire:model="year" class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500">
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Bulan --}}
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bulan</label>
                <select id="month" wire:model="month" class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Terapkan --}}
            <div class="flex items-end">
                <button 
                    wire:click="applyFilters"
                    class="w-full px-5 py-2.5 text-sm font-medium text-white transition-colors duration-200 rounded-lg shadow-sm bg-primary-600 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Terapkan
                </button>
            </div>
        </div>
    </div>

    {{-- 📊 Statistik Section --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="p-6 text-center transition-shadow duration-200 bg-white rounded-2xl shadow-sm hover:shadow-md dark:bg-gray-800">
            <h3 class="text-sm font-medium text-gray-500 uppercase dark:text-gray-400">Sesi Hari Ini</h3>
            <p class="mt-3 text-4xl font-bold text-gray-900 dark:text-white">{{ $sessionsToday }}</p>
        </div>

        <div class="p-6 text-center transition-shadow duration-200 bg-white rounded-2xl shadow-sm hover:shadow-md dark:bg-gray-800">
            <h3 class="text-sm font-medium text-gray-500 uppercase dark:text-gray-400">
                Sesi Bulan Ini ({{ $months[(int) $month] ?? '' }})
            </h3>
            <p class="mt-3 text-4xl font-bold text-gray-900 dark:text-white">{{ $sessionsThisMonth }}</p>
        </div>

        <div class="p-6 text-center transition-shadow duration-200 bg-white rounded-2xl shadow-sm hover:shadow-md dark:bg-gray-800">
            <h3 class="text-sm font-medium text-gray-500 uppercase dark:text-gray-400">Total Sesi</h3>
            <p class="mt-3 text-4xl font-bold text-gray-900 dark:text-white">{{ $totalSessions }}</p>
        </div>
    </div>

    {{-- 📈 Grafik Section --}}
    <div class="mt-10">
        <div class="p-6 bg-white rounded-2xl shadow-sm dark:bg-gray-800">
            <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-100">Tren Bulanan</h2>
            @livewire(\App\Filament\Widgets\MonthlySessionsChart::class, ['year' => $year, 'month' => $month])
        </div>
    </div>
</x-filament-panels::page>
