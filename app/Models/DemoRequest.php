<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoRequest extends Model
{
    protected $fillable = [
        'contact_name', 'email', 'phone', 'institution_name',
        'institution_type', 'district', 'student_count',
        'interested_modules', 'preferred_date', 'notes',
        'status', 'assigned_to', 'source', 'lead_id', 'converted_at',
    ];

    protected $casts = [
        'interested_modules' => 'array',
        'preferred_date'     => 'date',
        'converted_at'       => 'datetime',
    ];

    // ── Relationships ──

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    // ── Conversion ──

    /**
     * Convert this demo request into a CRM Lead.
     */
    public function convertToLead(?int $assignedTo = null, ?int $teamId = null): Lead
    {
        $lead = Lead::create([
            'name'              => $this->contact_name,
            'company'           => $this->institution_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'source'            => 'web',
            'status'            => 'new',
            'priority'          => 'medium',
            'assigned_to'       => $assignedTo ?? $this->assigned_to,
            'team_id'           => $teamId,
            'demo_request_id'   => $this->id,
            'notes'             => $this->buildConversionNotes(),
        ]);

        $this->update([
            'lead_id'      => $lead->id,
            'status'       => 'converted',
            'converted_at' => now(),
        ]);

        return $lead;
    }

    protected function buildConversionNotes(): string
    {
        $notes = "Converted from Demo Request #{$this->id}\n";
        $notes .= "Institution: {$this->institution_name} ({$this->institution_type})\n";
        if ($this->district) $notes .= "District: {$this->district}\n";
        if ($this->student_count) $notes .= "Students: {$this->student_count}\n";
        if ($this->interested_modules) {
            $notes .= "Interested Modules: " . implode(', ', $this->interested_modules) . "\n";
        }
        if ($this->notes) $notes .= "\nOriginal Notes: {$this->notes}";
        return $notes;
    }

    // ── Scopes ──

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeNotConverted(Builder $query): Builder
    {
        return $query->whereNull('converted_at');
    }
}
