@extends('layouts.app')

@section('content')

<h2>Rendez-vous</h2>

<form method="POST" action="/rdv">
@csrf
<input name="patient_id" class="form-control mb-2" placeholder="Patient ID">
<input name="medecin_id" class="form-control mb-2" placeholder="Medecin ID">
<input type="datetime-local" name="date" class="form-control mb-2">
<button class="btn btn-primary">Ajouter</button>
</form>

<table class="table mt-3">
<tr>
    <th>ID</th>
    <th>Date</th>
    <th>Status</th>
</tr>

@foreach($rdvs as $r)
<tr>
    <td>{{ $r->id }}</td>
    <td>{{ $r->date }}</td>
    <td>{{ $r->statut }}</td>
</tr>
@endforeach

</table>

@endsection