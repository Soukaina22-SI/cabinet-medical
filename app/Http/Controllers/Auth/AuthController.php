<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->boolean('remember'))) {

            $request->session()->regenerate();

            return match (Auth::user()->role) {
                'admin'      => redirect()->route('admin.dashboard'),
                'medecin'    => redirect()->route('medecin.dashboard'),
                'secretaire' => redirect()->route('secretaire.dashboard'),
                'patient'    => redirect()->route('patient.dashboard'),
                default      => redirect('/'),
            };
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nom'             => 'required|string|max:255',
            'prenom'          => 'required|string|max:255',
            'email'           => 'required|email|unique:users',
            'telephone'       => 'nullable|string|max:20',
            'date_naissance'  => 'required|date|before:-18 years',
            'sexe'            => 'required|in:M,F',
            'password'        => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'email'     => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'password'  => Hash::make($data['password']),
            'role'      => 'patient',
        ]);

        $patient = $user->patient()->create([
            'date_naissance' => $data['date_naissance'],
            'sexe'           => $data['sexe'],
        ]);

        $patient->dossierMedical()->create([
            'antecedents' => null,
            'allergies' => null,
            'groupe_sanguin' => null,
        ]);

        Auth::login($user);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Compte créé avec succès. Bienvenue !');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}