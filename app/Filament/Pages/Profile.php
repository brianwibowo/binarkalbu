<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Profil Saya';
    protected static string $view = 'filament.pages.profile';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        /** @var User $user */ // Petunjuk untuk editor
        $user = Auth::user();

        $this->profileForm->fill($user->toArray());
        $this->passwordForm->fill();
    }

    public function updateProfile(): void
    {
        /** @var User $user */ // Petunjuk untuk editor
        $user = Auth::user();
        
        $data = $this->profileForm->getState();
        $user->update($data);

        Notification::make()
            ->title('Profil berhasil diperbarui')
            ->success()
            ->send();

        $this->redirect(static::getUrl(), navigate: true);
    }

    public function updatePassword(): void
    {
        /** @var User $user */ // Petunjuk untuk editor
        $user = Auth::user();

        $data = $this->passwordForm->getState();
        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);

        Notification::make()
            ->title('Password berhasil diperbarui')
            ->success()
            ->send();

        $this->passwordForm->fill();
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Profil')
                    ->schema([
                        FileUpload::make('profile_photo_path')
                            ->label('Foto Profil')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('avatars'),
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
            ])
            ->model(Auth::user())
            ->statePath('profileData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ubah Password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->required()
                            ->currentPassword(),
                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->required()
                            ->confirmed(),
                        TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->required(),
                    ]),
            ])
            ->statePath('passwordData');
    }

    protected function getForms(): array
    {
        return [
            'profileForm',
            'passwordForm',
        ];
    }
}