
 
{{-- ============================================================ --}}
{{-- resources/views/auth/login.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Connexion')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card p-4" style="width: 420px">
        <div class="text-center mb-4">
            <i class="bi bi-hospital-fill text-primary fs-1"></i>
            <h4 class="mt-2 fw-bold">Cabinet Médical</h4>
            <p class="text-muted small">Connectez-vous à votre espace</p>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus placeholder="votre@email.com">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Mot de passe</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Se souvenir de moi</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Connexion
            </button>
        </form>
        <hr>
        <p class="text-center small mb-0">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="text-primary fw-semibold">S'inscrire</a>
        </p>
    </div>
</div>
@endsection
 
 