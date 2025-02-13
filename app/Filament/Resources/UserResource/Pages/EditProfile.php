<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\EditProfile as AuthEditProfile;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class EditProfile extends AuthEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('avatar')
                    ->collection('avatars')
                    ->required(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
