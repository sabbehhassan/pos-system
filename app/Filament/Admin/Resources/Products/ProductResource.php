<?php

namespace App\Filament\Admin\Resources\Products;

use App\Filament\Admin\Resources\Products\Pages\CreateProduct;
use App\Filament\Admin\Resources\Products\Pages\EditProduct;
use App\Filament\Admin\Resources\Products\Pages\ListProducts;
use App\Filament\Admin\Resources\Products\Schemas\ProductForm;
use App\Filament\Admin\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    /* =========================
       FORMS & TABLES
    ========================== */

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    /* =========================
       RBAC (SRS COMPLIANT)
    ========================== */

    // 👁️ Admin + Manager + Cashier can VIEW list
    public static function canViewAny(): bool
    {
        return auth()->check()
            && (
                auth()->user()->isAdmin()
                || auth()->user()->isManager()
                || auth()->user()->isCashier()
            );
    }

    // ✏️ ONLY Admin can EDIT
    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    // 🗑️ ONLY Admin can DELETE
    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    // 📌 Sidebar visible for all who can view
    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    /* =========================
       PAGES
    ========================== */

    public static function getPages(): array
    {
        return [
            'index'  => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit'   => EditProduct::route('/{record}/edit'),
        ];
    }
}