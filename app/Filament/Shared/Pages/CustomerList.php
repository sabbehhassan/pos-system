<?php

namespace App\Filament\Shared\Pages;

use Filament\Pages\Page;
use App\Models\Customer;
use BackedEnum;

class CustomerList extends Page
{
    protected string $view = 'filament.shared.pages.customer-list';

    // ✅ SAFE sidebar config (same pattern as AddCustomer)
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Customer List';
    protected static ?string $title = 'Customer List';

    /**
     * Admin + Cashier access
     */
    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'cashier']);
    }

    /**
     * Customers data
     */
    public function getCustomersProperty()
    {
        return Customer::latest()->get();
    }
}