<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_name')
                    ->required(),
                TextInput::make('architect_name')
                    ->required(),
                TextInput::make('professional_title')
                    ->required(),
                Textarea::make('short_bio')
                    ->columnSpanFull(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                Textarea::make('education')
                    ->columnSpanFull(),
                Textarea::make('experience')
                    ->columnSpanFull(),
                Textarea::make('design_philosophy')
                    ->columnSpanFull(),
                Textarea::make('specializations')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('whatsapp_number'),
                Textarea::make('whatsapp_message')
                    ->columnSpanFull(),
                TextInput::make('address'),
                TextInput::make('instagram_url')
                    ->url(),
                TextInput::make('facebook_url')
                    ->url(),
                TextInput::make('linkedin_url')
                    ->url(),
                TextInput::make('behance_url')
                    ->url(),
                TextInput::make('pinterest_url')
                    ->url(),
                FileUpload::make('profile_image')
                    ->image()
                    ->disk('public'),
                FileUpload::make('hero_image')
                    ->image()
                    ->disk('public'),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
            ]);
    }
}
