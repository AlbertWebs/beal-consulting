<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteService extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'number', 'title', 'description', 'description_html', 'media_asset_id', 'image_path', 'image_alt',
        'subservices', 'content', 'status', 'sort_order', 'seo_title', 'seo_description', 'updated_by', 'draft_content',
    ];

    protected function casts(): array
    {
        return ['subservices' => 'array', 'content' => 'array', 'draft_content' => 'array'];
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
