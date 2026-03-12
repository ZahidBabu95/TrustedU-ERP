<?php

namespace App\Filament\Resources\DemoRequests\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DemoRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Institution Details')->schema([
                TextInput::make('institution_name')->required()->maxLength(200)->columnSpanFull(),
                Select::make('institution_type')
                    ->options(['school' => 'School', 'college' => 'College', 'university' => 'University', 'madrasha' => 'Madrasha', 'other' => 'Other'])
                    ->required(),
                TextInput::make('district')->maxLength(100),
                TextInput::make('student_count')->maxLength(50)->label('Student Count (Range)'),
            ])->columns(2),

            Section::make('Contact Person')->schema([
                TextInput::make('contact_name')->required()->maxLength(150)->label('Name'),
                TextInput::make('email')->email()->required()->maxLength(150),
                TextInput::make('phone')->required()->maxLength(30),
            ])->columns(3),

            Section::make('Request Details')->schema([
                CheckboxList::make('interested_modules')
                    ->options([
                        'admission' => '🎓 Admission', 'fees'       => '💰 Fees',
                        'exam'      => '📝 Exam',      'accounts'   => '📊 Accounts',
                        'hr'        => '👥 HR',        'sms'        => '📱 SMS Gateway',
                        'library'   => '📚 Library',   'transport'  => '🚌 Transport',
                    ])->columns(3)->columnSpanFull(),
                DatePicker::make('preferred_date')->label('Preferred Demo Date')->minDate(today()),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'contacted' => 'Contacted', 'demo_done' => 'Demo Done', 'converted' => 'Converted', 'rejected' => 'Rejected'])
                    ->default('pending'),
                Textarea::make('notes')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
