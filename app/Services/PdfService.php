<?php
 
// ============================================================
// app/Services/PdfService.php
// ============================================================
namespace App\Services;
 
use App\Models\Ordonnance;
use Barryvdh\DomPDF\Facade\Pdf;
 
class PdfService
{
    public function generateOrdonnance(Ordonnance $ordonnance)
    {
        $pdf = Pdf::loadView('pdf.ordonnance', [
            'ordonnance'   => $ordonnance,
            'consultation' => $ordonnance->consultation,
            'medecin'      => $ordonnance->consultation->medecin,
            'patient'      => $ordonnance->consultation->patient,
        ]);
 
        $filename = 'ordonnance_' . $ordonnance->id . '_' . now()->format('Y-m-d') . '.pdf';
 
        // Sauvegarder le path dans la BDD
        $path = 'ordonnances/' . $filename;
        $ordonnance->update(['pdf_path' => $path]);
 
        return $pdf->download($filename);
    }
}
 