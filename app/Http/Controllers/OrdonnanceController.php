<?php

// ============================================================
// app/Http/Controllers/OrdonnanceController.php
// ============================================================
namespace App\Http\Controllers;
 
use App\Models\Ordonnance;
use App\Services\PdfService;
use Illuminate\Support\Facades\Auth;
 
class OrdonnanceController extends Controller
{
    public function exportPdf(Ordonnance $ordonnance, PdfService $pdfService)
    {
        $ordonnance->load(['consultation.medecin.user', 'consultation.patient.user']);
        return $pdfService->generateOrdonnance($ordonnance);
    }
}