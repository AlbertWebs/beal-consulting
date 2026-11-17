<?php

namespace App\Models;

use Database\Factories\ContentEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'title', 'slug', 'summary', 'content_html', 'category', 'author', 'location', 'scale', 'scope', 'outcomes', 'experience_source', 'media_asset_id', 'gallery_media_ids', 'image_alt', 'status', 'sort_order', 'is_featured', 'published_at', 'seo_title', 'seo_description', 'draft_data', 'updated_by'])]
class ContentEntry extends Model
{
    /** @use HasFactory<ContentEntryFactory> */
    use HasFactory;

    public const array TYPES = [
        'sector' => 'Sectors',
        'insight' => 'Insights & Intelligence',
        'project' => 'Projects & Experience',
    ];

    protected function casts(): array
    {
        return ['draft_data' => 'array', 'gallery_media_ids' => 'array', 'published_at' => 'datetime', 'is_featured' => 'boolean'];
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
