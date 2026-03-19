<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        // Infolist is now defined directly in UserResource::infolist()
        return $schema->components([]);
    }
}
