<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Translation extends Model
{
    /** @use HasFactory<\Database\Factories\TranslationFactory> */
    use HasFactory;

    protected $fillable = ['key', 'locale_id', 'content'];

    /**
     * Scope a query to filter by key.
     */
    public function scopeWhereKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }

    /**
     * Scope a query to filter by locale.
     */
    public function scopeWhereLocale(Builder $query, string $locale): Builder
    {
        return $query->with('locale')->whereHas('locale', fn (Builder $q) => $q->where('code', $locale));
    }

    /**
     * Scope a query to filter by tag.
     */
    public function scopeWhereTag(Builder $query, ?string $tag): Builder
    {
        return $query->with('tags')->whereHas('tags', fn (Builder $q) => $q->where('name', $tag));
    }

    /**
     * Scope a query to filter by content.
     */
    public function scopeWhereContentLike(Builder $query, string $content): Builder
    {
        return $query->where('content', 'LIKE', '%'.$content.'%');
    }

    /**
     * Get the locale that owns the translation.
     */
    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    /**
     * Get the tag associated with the translation.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_translation', 'translation_id', 'tag_id');
    }
}
