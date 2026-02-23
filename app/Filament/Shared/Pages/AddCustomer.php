<?php

namespace App\Filament\Shared\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use BackedEnum;

class AddCustomer extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected string $view = 'filament.shared.pages.add-customer';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Add Customer';
    protected static ?string $title = 'Add Customer';

     // 🔑 IMPORTANT: form state
    public array $data = [];

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'cashier']);
    }

    // 🔥 THIS IS THE KEY FIX
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Customer Name')
                ->required(),

            Forms\Components\TextInput::make('phone')
                ->label('Phone Number')
                ->required()
                ->unique('customers', 'phone'),

            Forms\Components\TextInput::make('shop_name')
                ->label('Shop Name'),

            Forms\Components\Textarea::make('shop_address')
                ->label('Shop Address'),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data'; // 🔥 CRITICAL
    }

    public function mount(): void
    {
        $this->form->fill(); // 🔥 initialize form
    }

    public function save(): void
    {
        $this->form->validate(); // 🔥 validate bound data

        \App\Models\Customer::create($this->data);

        Notification::make()
            ->title('Customer added successfully')
            ->success()
            ->send();

        $this->form->fill(); // reset form
    }
}