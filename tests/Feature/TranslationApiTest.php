<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate using Sanctum before each test
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_list_translations()
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        Translation::factory()->count(5)->create(['locale_id' => $locale->id]);

        $response = $this->getJson('/api/translations?locale=en');

        $response->assertOk()->assertJsonStructure(['success', 'message', 'data']);
    }

    public function test_can_create_translation()
    {
        $data = [
            'locale' => 'en',
            'key' => 'greet',
            'content' => 'Hello',
            'tag' => 'greeting',
        ];

        $response = $this->postJson('/api/translations', $data);

        $response->assertOk()->assertJsonFragment(['key' => 'greet']);
    }

    public function test_can_show_translation()
    {
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create(['locale_id' => $locale->id]);

        $response = $this->getJson("/api/translations/{$translation->id}");

        $response->assertOk()->assertJsonFragment(['id' => $translation->id]);
    }

    public function test_can_update_translation()
    {
        $locale = Locale::factory()->create(['code' => 'fr']);
        $translation = Translation::factory()->create();

        $updateData = [
            'locale' => 'fr',
            'key' => 'new_key',
            'content' => 'Nouveau contenu',
            'tag' => 'updated',
        ];

        $response = $this->putJson("/api/translations/{$translation->id}", $updateData);

        $response->assertOk()->assertJsonFragment(['key' => 'new_key']);
    }

    public function test_can_delete_translation()
    {
        $translation = Translation::factory()->create();

        $response = $this->deleteJson("/api/translations/{$translation->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    public function test_can_export_translations()
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        $translation = Translation::factory()->create([
            'locale_id' => $locale->id,
            'key' => 'test_key',
            'content' => 'Test',
        ]);

        $translation->tags()->create(['name' => 'test_tag']);

        $response = $this->getJson('/api/translations/export/en/test_tag');

        $response->assertOk()->assertJsonFragment([['key' => 'test_key', 'content' => 'Test']]);
    }
}
