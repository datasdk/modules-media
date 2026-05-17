<?php

namespace Modules\Media\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Media\Services\PDFGenerator;
use Modules\Tasks\Models\PDF;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Auth;

use Modules\Media\Models\Media;





class PDFController extends Controller
{
   
    /**
     * Generer og gem en PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function generate()
    {
        // Data til at vise i din PDF
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        // Generer PDF'en fra en view
        $pdfContent = (new PDFGenerator())->generatePDF('pdf_template', $data)->getContent();

        // Gem PDF'en i storage/app/pdf
        $fileName = 'document_' . time() . '.pdf';

        $filePath = "pdf/{$fileName}";

        Storage::put($filePath, $pdfContent);

        
        // Opret PDF entry i databasen
        $pdf = PDF::create([
            'user_id' => Auth::id(),
            'name' => $fileName,
            'size' => Storage::size($filePath),
            'path' => $filePath,
            'status' => 'generated',
        ]);

        return response()->json([
            'message' => 'PDF genereret og gemt.',
            'url' => url("/api/pdf/{$pdf->id}")
        ], Response::HTTP_CREATED);
    }

    
  
    
}
