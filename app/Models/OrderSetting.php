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
        'midtrans_enabled',
        'midtrans_server_key',
        'midtrans_client_key',
        'midtrans_is_production',
    ];

    protected $casts = [
        'midtrans_enabled' => 'boolean',
        'midtrans_is_production' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
