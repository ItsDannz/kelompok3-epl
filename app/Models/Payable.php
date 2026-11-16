<?php

namespace App\Models;

// RF-011: Ekstraksi penentuan bucket aging.

use App\Support\DebtAgingBucket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'document_number',
        'total',
        'paid',
        'due_date',
        'status',
        'note',
        'purchase_order_id',
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->hasMany(PayablePayment::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function getRemainingAttribute(): float
    {
        return max(0, ($this->total ?? 0) - ($this->paid ?? 0));
    }

    public function getAgingBucketAttribute(): string
    {
        return DebtAgingBucket::resolve($this->status, $this->due_date);
    }
}
