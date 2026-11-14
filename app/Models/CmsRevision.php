<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsRevision extends Model
{
    use HasFactory;

    protected $fillable = ['revisable_type', 'revisable_id', 'snapshot', 'user_id'];

    protected function casts(): array
    {
        return ['snapshot' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
