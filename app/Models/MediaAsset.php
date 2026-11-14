<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'disk', 'path', 'thumbnail_path', 'original_name', 'mime_type', 'file_size', 'width', 'height', 'alt_text', 'is_system', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class, 'media_asset_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(WebsiteService::class, 'media_asset_id');
    }

    public function contentEntries(): HasMany
    {
        return $this->hasMany(ContentEntry::class, 'media_asset_id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(SiteSetting::class, 'media_asset_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(WebsitePage::class, 'open_graph_media_id');
    }

    public function getUrlAttribute(): string
    {
        if ($this->disk === 'assets') {
            return '/assets/'.ltrim($this->path, '/');
        }

        return Storage::disk($this->disk)->url(ltrim($this->path, '/'));
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->disk === 'assets') {
            return '/assets/'.ltrim($this->thumbnail_path ?: $this->path, '/');
        }

        return Storage::disk($this->disk)->url(ltrim($this->thumbnail_path ?: $this->path, '/'));
    }
}
