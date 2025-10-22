<x-filament-panels::page>
    {{-- Bagian Filter --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-funnel" class="w-5 h-5" />
                <span>Filter Data</span>
            </div>
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-filament::input.wrapper>
                    <x-slot name="label">
                        Tahun
                    </x-slot>
                    <x-filament::input.select wire:model.live="year">
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            
            <div>
                <x-filament::input.wrapper>
                    <x-slot name="label">
                        Bulan
                    </x-slot>
                    <x-filament::input.select wire:model.live="month">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>

    {{-- Bagian Card Statistik - SEKARANG 4 CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        {{-- Card 1: Sesi Hari Ini --}}
        <x-filament::section>
            <div class="text-center space-y-2">
                <div class="flex justify-center">
                    <x-filament::icon 
                        icon="heroicon-o-calendar-days" 
                        class="w-8 h-8 text-primary-500"
                    />
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Sesi Hari Ini
                </h3>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $sessionsToday }}
                </p>
            </div>
        </x-filament::section>

        {{-- Card 2: Sesi Bulan Dipilih --}}
        <x-filament::section>
            <div class="text-center space-y-2">
                <div class="flex justify-center">
                    <x-filament::icon 
                        icon="heroicon-o-chart-bar" 
                        class="w-8 h-8 text-success-500"
                    />
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Sesi {{ $months[(int)$month] ?? '' }} {{ $year }}
                </h3>
                <p class="text-3xl font-bold text-success-600 dark:text-success-400">
                    {{ $sessionsInMonth }}
                </p>
            </div>
        </x-filament::section>

        {{-- Card 3: Sesi Tahun Dipilih (BARU!) --}}
        <x-filament::section>
            <div class="text-center space-y-2">
                <div class="flex justify-center">
                    <x-filament::icon 
                        icon="heroicon-o-calendar" 
                        class="w-8 h-8 text-warning-500"
                    />
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Sesi Tahun {{ $year }}
                </h3>
                <p class="text-3xl font-bold text-warning-600 dark:text-warning-400">
                    {{ $sessionsInYear }}
                </p>
            </div>
        </x-filament::section>

        {{-- Card 4: Total Semua Sesi --}}
        <x-filament::section>
            <div class="text-center space-y-2">
                <div class="flex justify-center">
                    <x-filament::icon 
                        icon="heroicon-o-clipboard-document-list" 
                        class="w-8 h-8 text-info-500"
                    />
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Total Semua Sesi
                </h3>
                <p class="text-3xl font-bold text-info-600 dark:text-info-400">
                    {{ $totalSessions }}
                </p>
            </div>
        </x-filament::section>
    </div>
    
    {{-- Bagian 2 Ranking: Bulan & Tahun --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Ranking Psikolog Teraktif BULAN INI --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-trophy" class="w-5 h-5 text-success-500" />
                    <span>Psikolog Teraktif Bulan Ini</span>
                </div>
            </x-slot>
            
            <x-slot name="description">
                Periode: {{ $months[(int)$month] ?? '' }} {{ $year }}
            </x-slot>

            <div class="mt-4 space-y-3">
                @forelse($topPsychologistsMonth as $psychologist)
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div class="flex items-center gap-3">
                            {{-- Medali untuk top 3 --}}
                            @if($loop->iteration <= 3)
                                <div class="flex items-center justify-center w-8 h-8 rounded-full
                                    {{ $loop->iteration == 1 ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                    {{ $loop->iteration == 2 ? 'bg-gray-100 dark:bg-gray-800' : '' }}
                                    {{ $loop->iteration == 3 ? 'bg-orange-100 dark:bg-orange-900/30' : '' }}
                                ">
                                    <span class="text-lg font-bold
                                        {{ $loop->iteration == 1 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                        {{ $loop->iteration == 2 ? 'text-gray-600 dark:text-gray-400' : '' }}
                                        {{ $loop->iteration == 3 ? 'text-orange-600 dark:text-orange-400' : '' }}
                                    ">
                                        {{ $loop->iteration }}
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center justify-center w-8 h-8">
                                    <span class="text-sm font-semibold text-gray-500">
                                        {{ $loop->iteration }}
                                    </span>
                                </div>
                            @endif
                            
                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $psychologist->name }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <x-filament::badge color="success" size="lg">
                                {{ $psychologist->session_count }} Sesi
                            </x-filament::badge>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <x-filament::icon 
                            icon="heroicon-o-inbox" 
                            class="w-12 h-12 mx-auto text-gray-400"
                        />
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data sesi di bulan ini
                        </p>
                    </div>
                @endforelse
            </div>
        </x-filament::section>

        {{-- Ranking Psikolog Teraktif TAHUN INI (BARU!) --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-star" class="w-5 h-5 text-warning-500" />
                    <span>Psikolog Teraktif Tahun Ini</span>
                </div>
            </x-slot>
            
            <x-slot name="description">
                Periode: Tahun {{ $year }}
            </x-slot>

            <div class="mt-4 space-y-3">
                @forelse($topPsychologistsYear as $psychologist)
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div class="flex items-center gap-3">
                            {{-- Medali untuk top 3 --}}
                            @if($loop->iteration <= 3)
                                <div class="flex items-center justify-center w-8 h-8 rounded-full
                                    {{ $loop->iteration == 1 ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                    {{ $loop->iteration == 2 ? 'bg-gray-100 dark:bg-gray-800' : '' }}
                                    {{ $loop->iteration == 3 ? 'bg-orange-100 dark:bg-orange-900/30' : '' }}
                                ">
                                    <span class="text-lg font-bold
                                        {{ $loop->iteration == 1 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                        {{ $loop->iteration == 2 ? 'text-gray-600 dark:text-gray-400' : '' }}
                                        {{ $loop->iteration == 3 ? 'text-orange-600 dark:text-orange-400' : '' }}
                                    ">
                                        {{ $loop->iteration }}
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center justify-center w-8 h-8">
                                    <span class="text-sm font-semibold text-gray-500">
                                        {{ $loop->iteration }}
                                    </span>
                                </div>
                            @endif
                            
                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $psychologist->name }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <x-filament::badge color="warning" size="lg">
                                {{ $psychologist->session_count }} Sesi
                            </x-filament::badge>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <x-filament::icon 
                            icon="heroicon-o-inbox" 
                            class="w-12 h-12 mx-auto text-gray-400"
                        />
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data sesi di tahun ini
                        </p>
                    </div>
                @endforelse
            </div>
        </x-filament::section>
    </div>

    {{-- Bagian Grafik --}}
    <div class="mt-6">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-chart-bar-square" class="w-5 h-5" />
                    <span>Grafik Sesi Bulanan</span>
                </div>
            </x-slot>
            
            @livewire(\App\Filament\Widgets\MonthlySessionsChart::class)
        </x-filament::section>
    </div>
</x-filament-panels::page>