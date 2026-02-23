<?php

namespace App\Filament\Shared\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class InventoryReport extends Page
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::Cube;
    protected static ?string $navigationLabel = 'Inventory Report';

    // Filament v3: NON-static view
    protected string $view = 'filament.admin.pages.inventory-report';

    public static function getNavigationGroup(): string
    {
        return 'Reports';
    }

    /* ===== DATA ===== */
    public $products;

    public int $totalProducts = 0;
    public int $totalStockQty = 0;
    public float $inventoryValue = 0;

    public function mount(): void
    {
        $this->loadInventory();
    }

    public function loadInventory(): void
    {
        $this->products = Product::with('category')->get();

        $this->totalProducts = $this->products->count();

        // stock column
        $this->totalStockQty = $this->products->sum('stock');

        // inventory value = stock × price
        $this->inventoryValue = $this->products->sum(function ($product) {
            return (int) $product->stock * (float) $product->price;
        });
    }
}