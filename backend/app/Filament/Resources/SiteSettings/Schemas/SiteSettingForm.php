<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Support\ImageCompressor;

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
                TextInput::make('youtube_url')
                    ->url(),
                TextInput::make('pinterest_url')
                    ->url(),
                FileUpload::make('profile_image')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('800')
                    ->disk('public')
                    ->saveUploadedFileUsing(
                        fn($file) => ImageCompressor::store($file, 'public', null, 800, 75)
                    ),
                FileUpload::make('hero_image')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->disk('public')
                    ->saveUploadedFileUsing(
                        fn($file) => ImageCompressor::store($file, 'public', null, 1920, 75)
                    ),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
            ]);
    }
}