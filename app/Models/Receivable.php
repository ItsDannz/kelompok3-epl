<?php

namespace App\Models;

// RF-011: Ekstraksi penentuan bucket aging.

use App\Support\DebtAgingBucket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'transaction_id',
        'invoice',
        'total',
        'paid',
        'due_date',
        'status',
        'note',
        'collection_notes',
    ];

    protected $casts = [
        'total' => 'float',
        'paid' => 'float',
        'due_date' => 'date',
    ];

    protected $appends = [
        'remaining',
        'aging_bucket',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function payments()
    {
        return $this->hasMany(ReceivablePayment::class);
    }

    public function campaignLogs()
    {
        return $this->hasMany(CustomerCampaignLog::class);
    }

    public function getRemainingAttribute(): float
    {
        return max(0, ($this->total ?? 0) - ($this->paid ?? 0));
    }

    public function getAgingBucketAttribute(): string
    {
        return DebtAgingBucket::resolve($this->status, $this->due_date);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now());
    }

    public function scopeDueWithinDays($query, int $days = 7)
    {
        return $query->where('status', '!=', 'paid')
            ->whereBetween('due_date', [now()->format('Y-m-d'), now()->addDays($days)->format('Y-m-d')]);
    }

    public function scopeByAgingBucket($query, string $bucket)
    {
        $query->where('status', '!=', 'paid')->whereNotNull('due_date');

        if ($bucket === 'current') {
            $query->whereDate('due_date', '>=', now());
        } else {
            $query->whereDate('due_date', '<', now());

            $daysOverdue = match ($bucket) {
                '0-30' => 30,
                '31-60' => 60,
                '61-90' => 90,
                default => 365,
            };

            $minDays = match ($bucket) {
                '0-30' => 0,
                '31-60' => 31,
                '61-90' => 61,
                default => 91,
            };

            $query->whereBetween('due_date', [
                now()->subDays($daysOverdue)->format('Y-m-d'),
                now()->subDays($minDays)->format('Y-m-d'),
            ]);
        }

        return $query;
    }
}
