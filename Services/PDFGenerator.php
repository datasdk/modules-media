<?php

namespace Modules\Media\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PDFGenerator
{
    protected Dompdf $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'Verdana');
        $this->dompdf = new Dompdf($options);
    }

    /**
     * Generer en PDF fra en Blade view og returner en sti eller UploadedFile.
     *
     * @param string $view Blade view
     * @param array $data Data til view
     * @param bool $save Gem fil til disk eller ej
     * @param string $disk Disknavn (skal eksistere i config/filesystems.php)
     * @param bool $asUploadedFile Returner som UploadedFile i stedet for sti
     * @return string|UploadedFile
     */
    public function generatePDF(
        string $view,
        array $data = [],
        bool $save = true,
        string $disk = 'uploads',
        bool $asUploadedFile = false
    ) {
        // Render HTML
        $html = view($view, $data)->render();

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        $output = $this->dompdf->output();

        if ($save) {
            $fileName = 'document_' . uniqid() . '.pdf';
            
            // Gem fil
            Storage::disk($disk)->put($fileName, $output);

            // Lav sti der kan bruges af MediaLibrary
            $absolutePath = Storage::disk($disk)->path($fileName);
            $absolutePath = str_replace('\\', '/', $absolutePath); // For Windows

            if ($asUploadedFile) {
                // Returner som UploadedFile
                return new UploadedFile(
                    $absolutePath,
                    $fileName,
                    'application/pdf',
                    null,
                    true // $test
                );
            }

            return $absolutePath;
        }

        // Returner raw PDF output
        return $output;
    }
}
