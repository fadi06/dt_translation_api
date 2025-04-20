<?php

namespace App\Repositories;

use App\Http\Resources\TranslationResource;
use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;

class TranslationRepository
{
    /**
     * Get all translations based on request parameters.
     *
     * @return Collection<Translation>
     */
    public function getAll(array $filters = [], $perPage = 50): array
    {
        $query = Translation::query()->whereLocale($filters['locale']);

        if (isset($filters['key'])) {
            $query->whereKey($filters['key']);
        }

        if (isset($filters['tag'])) {
            $query->whereTag($filters['tag']);
        }

        if (isset($filters['content'])) {
            $query->whereContentLike($filters['content']);
        }

        $translations = $query->paginate($perPage);

        // Convert to array to modify the `data` only
        $paginated = $translations->toArray();
        $paginated['data'] = TranslationResource::collection($translations)->resolve();

        return $paginated;

    }

    /**
     * Create a new translation.
     */
    public function create(array $data): Translation
    {
        $locale = Locale::firstOrCreate(['code' => $data['locale']]);

        $translation = Translation::create([
            'key' => $data['key'],
            'locale_id' => $locale->id,
            'content' => $data['content'],
        ]);

        $tag = Tag::firstOrCreate(['name' => $data['tag']]);
        $translation->tags()->sync([$tag->id]);

        return $translation->load('tags:id,name', 'locale:id,code');
    }

    /**
     * Get a translation by its ID.
     */
    public function find(Translation $translation): Translation
    {
        return $translation->load('tags:id,name', 'locale:id,code');
    }

    /**
     * Update an existing translation.
     */
    public function update(Translation $translation, array $data): Translation
    {
        $locale = Locale::firstOrCreate(['code' => $data['locale']]);

        $translation->update([
            'key' => $data['key'],
            'content' => $data['content'],
            'locale_id' => $locale->id,
        ]);

        $tag = Tag::firstOrCreate(['name' => $data['tag']]);
        $translation->tags()->sync([$tag->id]);

        return $translation->load('tags:id,name', 'locale:id,code');
    }

    /**
     * Delete a translation.
     */
    public function delete(Translation $translation): void
    {
        $translation->delete();
    }

    /**
     * Export translations as an associative array for a specific locale and optional tag.
     */
    public function export(string $locale, ?string $tag = null): array
    {
        $translations = [];

        Translation::where('locale_id', 1)
            ->select(['key', 'content'])
            ->chunk(100000, function ($chunk) use (&$translations) {
                foreach ($chunk as $translation) {
                    $translations[] = $translation->toArray();
                }
            });

        return $translations;
    }

    public function search($query)
    {
        info($query);
        return Translation::whereAny([
                'key',
                'content',
            ], 'like', "%{$query}%")->get();
    }
}
