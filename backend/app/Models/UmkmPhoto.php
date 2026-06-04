<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UmkmPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'umkm_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'is_primary',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'is_primary' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function getUrlAttribute(): string
    {
        return asset("storage/umkm-photos/{$this->filename}");
    }
}