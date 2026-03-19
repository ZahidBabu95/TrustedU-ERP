<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class EmployeeDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $kb = $this->file_size ?? 0;
        if ($kb >= 1024) {
            return round($kb / 1024, 1) . ' MB';
        }
        return $kb . ' KB';
    }
}
