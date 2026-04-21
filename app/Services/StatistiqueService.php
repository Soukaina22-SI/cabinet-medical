<?php
// ============================================================
// app/Services/StatistiqueService.php
// ============================================================
namespace App\Services;
 
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Models\Medecin;
use Illuminate\Support\Facades\DB;
 
class StatistiqueService
{
    public function rdvsParMois(): array
    {
        $data = RendezVous::selectRaw('MONTH(date_heure) as mois, COUNT(*) as total')
            ->whereYear('date_heure', now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
 
        $mois   = [];
        $totaux = [];
        $moisNoms = ['Jan','Fév','Mar','Avr','Mai','Juin','Jul','Aoû','Sep','Oct','Nov','Déc'];
 
        foreach ($data as $row) {
            $mois[]   = $moisNoms[$row->mois - 1];
            $totaux[] = $row->total;
        }
 
        return ['labels' => $mois, 'data' => $totaux];
    }
 
    public function rdvsParStatut(): array
    {
        $data = RendezVous::selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->get()
            ->pluck('total', 'statut');
 
        return [
            'labels' => $data->keys()->toArray(),
            'data'   => $data->values()->toArray(),
        ];
    }
 
    public function consultationsParMedecin(): array
    {
        $data = Consultation::selectRaw('medecin_id, COUNT(*) as total')
            ->with('medecin.user')
            ->groupBy('medecin_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();
 
        return [
            'labels' => $data->map(fn($c) => $c->medecin->user->nom_complet)->toArray(),
            'data'   => $data->pluck('total')->toArray(),
        ];
    }
}
 