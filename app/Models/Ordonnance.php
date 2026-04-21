<?php

// ============================================================
// app/Models/Ordonnance.php
// ============================================================
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Ordonnance extends Model
{
    protected $fillable = [
        'consultation_id', 'medicaments', 'instructions', 'pdf_path'
    ];
 
    protected $casts = ['medicaments' => 'array'];
 
    public function consultation() { return $this->belongsTo(Consultation::class); }
}
 