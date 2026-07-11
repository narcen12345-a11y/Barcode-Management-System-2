<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarcodeHistory extends Model
{
    protected $fillable = [
        'barcode_id',
        'field_name',
        'old_value',
        'new_value',
        'changed_by',
        'change_reason',
    ];

    public function barcode(): BelongsTo
    {
        return $this->belongsTo(Barcode::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
