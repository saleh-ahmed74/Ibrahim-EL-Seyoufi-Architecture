<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')->relationship('category','name')->searchable()->preload(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('location'),
                TextInput::make('year')
                    ->numeric(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Repeater::make('images')->relationship()->schema([
                    FileUpload::make('image_path')->disk('public')->directory('projects')->image()->required(),
                    TextInput::make('alt_text'),
                    TextInput::make('sort_order')->numeric()->default(0),
                    \Filament\Forms\Components\Toggle::make('is_cover')->default(false),
                ])->reorderable()->columnSpanFull(),
            ]);
    }
}
