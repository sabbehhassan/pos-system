<?php

namespace App\Filament\Admin\Resources\Users;

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Schemas\UserForm;
use App\Filament\Admin\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // ✅ Icon is SAFE
    protected static string|BackedEnum|null $navigationIcon =
    Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    /* =========================
       FORMS & TABLES
    ========================== */

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    /* =========================
       ACCESS CONTROL (RBAC)
    ========================== */

    // 🔐 Admin only
    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    // 🔐 Hide sidebar entry for non-admins
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /* =========================
       PAGES
    ========================== */

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}