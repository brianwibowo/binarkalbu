<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Kolom Pertama: Form Profil --}}
        <form wire:submit="updateProfile">
            {{ $this->profileForm }}
            
            <div class="mt-6">
                <x-filament::button type="submit">
                    Simpan Perubahan Profil
                </x-filament::button>
            </div>
        </form>
        
        {{-- Kolom Kedua: Form Password --}}
        <form wire:submit="updatePassword">
            {{ $this->passwordForm }}

            <div class="mt-6">
                <x-filament::button type="submit">
                    Ubah Password
                </x-filament::button>
            </div>
        </form>

    </div>
</x-filament-panels::page>