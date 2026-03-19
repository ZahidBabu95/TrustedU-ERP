<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        // Form is now defined directly in UserResource::form()
        return $schema->components([]);
    }
}
