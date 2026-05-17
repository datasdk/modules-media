<?php

namespace Modules\Media\Database\Factories;

use Modules\Media\Models\Folders;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoldersFactory extends Factory
{
    protected $model = Folders::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'parent_id' => null, // Kan ændres dynamisk i tests
        ];
    }
}
