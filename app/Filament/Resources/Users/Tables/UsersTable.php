<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        // Table is now defined directly in UserResource::table()
        return $table;
    }
}
