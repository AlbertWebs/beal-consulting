<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_page_id', 'section_key', 'label', 'content', 'media_asset_id', 'is_enabled', 'sort_order', 'updated_by', 'draft_content', 'draft_media_asset_id', 'draft_enabled',
    ];

    protected function casts(): array
    {
        return ['content' => 'array', 'draft_content' => 'array', 'is_enabled' => 'boolean', 'draft_enabled' => 'boolean'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(WebsitePage::class, 'website_page_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function draftMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'draft_media_asset_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
