{{-- resources/views/doctor/schedule.blade.php --}}
@extends('layouts.app')
@section('title', 'Mes Disponibilités')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mes Disponibilités</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-xl-8">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('doctor.dashboard') }}" class="btn btn-sm btn-light rounded-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Mes Disponibilités</h5>
        <p class="text-muted small mb-0">Définissez vos horaires de travail par jour</p>
    </div>
</div>

<form method="POST" action="{{ route('doctor.schedule.update') }}">
@csrf @method('PUT')

<div class="stat-card mb-4">
    <h6 class="fw-semibold mb-4 text-primary">
        <i class="bi bi-calendar-week me-2"></i>Planning hebdomadaire
    </h6>

    @foreach([1=>'Lundi', 2=>'Mardi', 3=>'Mercredi', 4=>'Jeudi', 5=>'Vendredi', 6=>'Samedi', 0=>'Dimanche'] as $day => $dayName)
    @php $schedule = $schedules[$day] ?? null; @endphp

    <div class="row g-3 align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
        <div class="col-md-3 d-flex align-items-center gap-2">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input day-toggle" type="checkbox"
                       name="days[{{ $day }}][enabled]" value="1"
                       id="day_{{ $day }}"
                       {{ $schedule ? 'checked' : '' }}
                       onchange="toggleDay({{ $day }})">
                <label class="form-check-label fw-semibold" for="day_{{ $day }}">
                    {{ $dayName }}
                </label>
            </div>
        </div>

        <div class="col-md-9" id="dayFields_{{ $day }}"
             style="{{ $schedule ? '' : 'opacity:.4;pointer-events:none' }}">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">De</label>
                    <input type="time" name="days[{{ $day }}][start_time]"
                           class="form-control form-control-sm"
                           value="{{ $schedule?->start_time ?? '09:00' }}"
                           style="width:110px">
                </div>
                <div class="col-auto pt-3">
                    <i class="bi bi-arrow-right text-muted"></i>
                </div>
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">À</label>
                    <input type="time" name="days[{{ $day }}][end_time]"
                           class="form-control form-control-sm"
                           value="{{ $schedule?->end_time ?? '17:00' }}"
                           style="width:110px">
                </div>
                <div class="col-auto pt-3">
                    @if($schedule)
                        @php
                            $start = \Carbon\Carbon::parse($schedule->start_time);
                            $end   = \Carbon\Carbon::parse($schedule->end_time);
                            $hours = $start->diffInMinutes($end) / 60;
                        @endphp
                        <span class="badge bg-success-subtle text-success">
                            {{ $hours }}h de travail
                        </span>
                    @else
                        <span class="badge bg-light text-muted">Repos</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary px-4">Annuler</a>
    <button type="submit" class="btn btn-primary px-5">
        <i class="bi bi-check-lg me-1"></i>Enregistrer mes disponibilités
    </button>
</div>

</form>
</div>
</div>
@endsection

@push('scripts')
<script>
function toggleDay(day) {
    const checked = document.getElementById(`day_${day}`).checked;
    const fields  = document.getElementById(`dayFields_${day}`);
    fields.style.opacity          = checked ? '1' : '0.4';
    fields.style.pointerEvents    = checked ? 'auto' : 'none';
}
</script>
@endpush
