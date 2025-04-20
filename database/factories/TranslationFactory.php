<?php

namespace Database\Factories;

use App\Models\Locale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $localeIds = Locale::pluck('id');

        return [
            'key' => $this->faker->unique()->slug(),
            'locale_id' => $localeIds->isNotEmpty() ? $localeIds->random() : Locale::factory()->create()->id,
            'content' => $this->faker->sentence(),
        ];
    }
}
