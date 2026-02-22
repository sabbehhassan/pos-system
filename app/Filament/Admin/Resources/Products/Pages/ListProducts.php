<?php

namespace App\Filament\Admin\Resources\Products\Pages;

use App\Filament\Admin\Resources\Products\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    /**
     * Header buttons (New Product)
     */
    protected function getHeaderActions(): array
    {
        // 🔒 ONLY Admin sees "New Product"
        if (! auth()->user()->isAdmin()) {
            return [];
        }

        return [
            Actions\CreateAction::make(),
        ];
    }
}