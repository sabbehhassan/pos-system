<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Product Name')
                ->required()
                ->maxLength(255),

            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->searchable()
                ->required(),

            TextInput::make('price')
                ->numeric()
                ->required(),

            TextInput::make('stock')
                ->numeric()
                ->required(),
        ]);
    }
}
