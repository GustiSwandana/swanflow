<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'client_name',
        'client_phone',
        'project_title',
        'amount',
        'discount',
        'discount_label',
        'is_free',
        'gdrive_url',
        'stored_file_id',
        'folder_id',
        'notes',
        'status',
        'payment_proof_path',
        'payment_submitted_at',
        'verified_at',
        'rejection_reason',
        'wallet_id',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'is_free' => 'boolean',
        'payment_submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->token)) {
                $order->token = Str::lower(Str::random(12));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function storedFile(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(OrderDeliverable::class)->orderBy('sort_order');
    }

    public function getFinalAmountAttribute(): float
    {
        if ($this->is_free) {
            return 0.0;
        }

        return (float) max(0, (float) $this->amount - (float) $this->discount);
    }

    public function getClientUrlAttribute(): string
    {
        return url('/p/'.$this->token);
    }

    public function getWhatsappShareUrlAttribute(): ?string
    {
        if (empty($this->client_phone)) {
            return null;
        }

        // Clean phone number to Indonesian international format
        $phone = preg_replace('/[^0-9]/', '', $this->client_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        $link = $this->client_url;
        $text = $this->is_free
            ? "Halo Kak {$this->client_name},\n\nFile foto/video dokumentasi \"{$this->project_title}\" telah selesai kami proses dan siap diakses. Silakan buka tautan resmi berikut untuk mengunduh berkas Anda:\n\n{$link}\n\nTerima kasih! 🙏"
            : "Halo Kak {$this->client_name},\n\nFile foto/video dokumentasi \"{$this->project_title}\" telah selesai kami proses. Silakan buka tautan resmi berikut untuk rincian pembayaran dan membuka akses berkas Anda:\n\n{$link}\n\nTerima kasih! 🙏";

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($text);
    }
}
