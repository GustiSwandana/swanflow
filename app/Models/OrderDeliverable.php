<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDeliverable extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'title',
        'type',
        'url',
        'stored_file_id',
        'folder_id',
        'sort_order',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function storedFile(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
