<?php

namespace Database\Factories;

use App\Models\MorningWorship;
use Illuminate\Database\Eloquent\Factories\Factory;

class MorningWorshipFactory extends Factory
{
    protected $model = MorningWorship::class;

    public function definition(): array
    {
        return [
            'guid' => $this->faker->unique()->uuid(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'first_published' => $this->faker->dateTimeThisYear(),
            'duration' => $this->faker->numberBetween(60, 600),
            'duration_formatted' => '00:00', // Placeholder
            'video_url' => $this->faker->url(),
            'image_url' => $this->faker->imageUrl(),
            'subtitles' => null,
            'watched_at' => null,
        ];
    }
}
