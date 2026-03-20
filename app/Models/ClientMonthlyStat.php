<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientMonthlyStat extends Model
{
    protected $fillable = [
        'client_id', 'year', 'month',
        'active_students', 'total_students', 'notes',
    ];

    protected $casts = [
        'year'            => 'integer',
        'month'           => 'integer',
        'active_students' => 'integer',
        'total_students'  => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function getPeriodAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }
}
