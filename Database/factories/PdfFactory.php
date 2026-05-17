<?php

namespace Modules\Media\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Media\Models\Pdf;

class PdfFactory extends Factory
{
    protected $model = Pdf::class;

    public function definition()
    {
        $name = $this->faker->word . '-' . time();
        $html = '<h1>' . $this->faker->sentence . '</h1><p>' . $this->faker->paragraph . '</p>';
        
        // Generer og gem PDF
        $filePath = Pdf::create($name, $html);

        return [
            'name' => $name,
            'path' => $filePath, // Gemmer PDF'ens sti i databasen
        ];
    }
}
