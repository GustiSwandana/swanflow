<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'studio_name',
        'bank_instructions',
        'qris_image_path',
        'admin_pin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
