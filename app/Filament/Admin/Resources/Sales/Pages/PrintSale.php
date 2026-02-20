<?php

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Filament\Admin\Resources\Sales\SaleResource;
use Filament\Resources\Pages\ViewRecord;

class PrintSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    // 👇 record-based custom page
    protected static ?string $slug = 'print';

    // Filament v2 → NON-static
    protected string $view = 'filament.admin.resources.sales.pages.print-sale';
}