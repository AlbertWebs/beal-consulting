<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsitePage extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key', 'title', 'slug', 'status', 'navigation_label', 'show_in_navigation', 'sort_order',
        'seo_title', 'seo_description', 'open_graph_media_id', 'published_at', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['show_in_navigation' => 'boolean', 'published_at' => 'datetime'];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function openGraphMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'open_graph_media_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && ($this->published_at === null || $this->published_at->isPast());
    }
}
