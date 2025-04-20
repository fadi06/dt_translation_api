<?php

namespace Tests\Unit;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TranslationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TranslationRepository;
    }

    /**
     * Helper method to create a test translation
     */
    protected function createTestTranslation(string $localeCode, string $key, string $content, string $tagName): Translation
    {
        $locale = Locale::factory()->create(['code' => $localeCode]);
        $tag = Tag::create(['name' => $tagName]);

        $translation = Translation::create([
            'key' => $key,
            'content' => $content,
            'locale_id' => $locale->id,
        ]);

        $translation->tags()->attach($tag->id);

        return $translation;
    }

    /**
     * Helper method to assert translation and tag creation
     */
    protected function assertTranslationCreated(Translation $translation, array $data): void
    {
        $this->assertInstanceOf(Translation::class, $translation);
        $this->assertEquals($data['key'], $translation->key);
        $this->assertEquals($data['content'], $translation->content);
        $this->assertEquals($data['locale'], $translation->locale->code);

        $this->assertDatabaseHas('locales', ['code' => $data['locale']]);
        $this->assertDatabaseHas('tags', ['name' => $data['tag']]);
        $this->assertTrue($translation->tags->pluck('name')->contains($data['tag']));
    }

    /**
     * Test for creating a translation
     */
    public function test_can_create_translation()
    {
        $data = [
            'locale' => 'en',
            'key' => 'welcome',
            'content' => 'Welcome to our app!',
            'tag' => 'general',
        ];

        $translation = $this->repository->create($data);

        // Assert Translation was created correctly
        $this->assertTranslationCreated($translation, $data);
    }

    /**
     * Test for updating a translation
     */
    public function test_can_update_translation()
    {
        // Create initial translation with locale and tag
        $translation = $this->createTestTranslation('en', 'welcome', 'Welcome!', 'general');

        // New update data
        $data = [
            'locale' => 'fr',
            'key' => 'welcome_updated',
            'content' => 'Bienvenue!',
            'tag' => 'homepage',
        ];

        // Call the update method
        $updated = $this->repository->update($translation, $data);

        // Assert updated values
        $this->assertTranslationCreated($updated, $data);
    }

    /**
     * Test for deleting a translation
     */
    public function test_can_delete_translation()
    {
        $translation = $this->createTranslation();

        $this->repository->delete($translation);

        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    /**
     * Test for exporting translations
     */
    public function test_can_export_translations()
    {
        $translation = $this->createTranslation();
        $tag = Tag::create(['name' => 'firewall']);
        $translation->tags()->attach($tag->id);

        $result = $this->repository->export('en', 'firewall');
        $this->assertArrayHasKey('key', $result[0]);
        $this->assertEquals('Welcome to our app!', $result[0]['content']);
    }

    protected function createTranslation($tag = 'general')
    {
        $data = [
            'locale' => 'en',
            'key' => 'welcome',
            'content' => 'Welcome to our app!',
            'tag' => $tag,
        ];

        return $this->repository->create($data);
    }
}
