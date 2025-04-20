<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TranslationPerformanceTest extends TestCase
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

        $start = microtime(true); // Start measuring time

        $response = $this->getJson('/api/translations?locale=en');

        $duration = number_format((microtime(true) - $start) * 1000, 2); // Time in ms

        $response->assertOk()->assertJsonStructure(['success', 'message', 'data']);
        echo "GET /api/translations took {$duration}ms\n";  // Print time
        $this->assertLessThan(500, $duration, "GET /api/translations took {$duration}ms");
    }

    public function test_can_create_translation()
    {
        $data = [
            'locale' => 'en',
            'key' => 'greet',
            'content' => 'Hello',
            'tag' => 'greeting',
        ];

        $start = microtime(true); // Start measuring time

        $response = $this->postJson('/api/translations', $data);

        $duration = number_format((microtime(true) - $start) * 1000, 2); // Time in ms

        $response->assertOk()->assertJsonFragment(['key' => 'greet']);
        echo "POST /api/translations took {$duration}ms\n";  // Print time
        $this->assertLessThan(500, $duration, "POST /api/translations took {$duration}ms");
    }

    public function test_can_show_translation()
    {
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create(['locale_id' => $locale->id]);

        $start = microtime(true); // Start measuring time

        $response = $this->getJson("/api/translations/{$translation->id}");

        $duration = number_format((microtime(true) - $start) * 1000, 2); // Time in ms

        $response->assertOk()->assertJsonFragment(['id' => $translation->id]);
        echo "GET /api/translations/{$translation->id} took {$duration}ms\n";  // Print time
        $this->assertLessThan(500, $duration, "GET /api/translations/{$translation->id} took {$duration}ms");
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

        $start = microtime(true); // Start measuring time

        $response = $this->putJson("/api/translations/{$translation->id}", $updateData);

        $duration = number_format((microtime(true) - $start) * 1000, 2); // Time in ms

        $response->assertOk()->assertJsonFragment(['key' => 'new_key']);
        echo "PUT /api/translations/{$translation->id} took {$duration}ms\n";  // Print time
        $this->assertLessThan(500, $duration, "PUT /api/translations/{$translation->id} took {$duration}ms");
    }

    public function test_can_delete_translation()
    {
        $translation = Translation::factory()->create();

        $start = microtime(true); // Start measuring time

        $response = $this->deleteJson("/api/translations/{$translation->id}");

        $duration = number_format((microtime(true) - $start) * 1000, 2); // Time in ms

        $response->assertOk();
        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
        echo "DELETE /api/translations/{$translation->id} took {$duration}ms\n";  // Print time
        $this->assertLessThan(500, $duration, "DELETE /api/translations/{$translation->id} took {$duration}ms");
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

        $start = microtime(true); // Start measuring time

        $response = $this->getJson('/api/translations/export/en/test_tag');

        $duration = number_format((microtime(true) - $start) * 1000, 2); // Time in ms

        $response->assertOk()->assertJsonFragment([['key' => 'test_key', 'content' => 'Test']]);
        echo "GET /api/translations/export/en/test_tag took {$duration}ms\n";  // Print time
        $this->assertLessThan(700, $duration, "GET /api/translations/export/en/test_tag took {$duration}ms");
    }
}
