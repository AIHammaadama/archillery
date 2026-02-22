<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'uploaded_by',
        'vendor_id',
        'file_path',
        'original_filename',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
