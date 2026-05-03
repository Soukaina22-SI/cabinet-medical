<?php
// app/Http/Controllers/Auth/RegisterController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8|confirmed',
            'phone'          => 'required|string|max:20',
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:male,female',
            'cin'            => 'nullable|string|max:20|unique:patients,cin',
            'address'        => 'nullable|string|max:255',
            'blood_type'     => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        ], [
            'email.unique'       => 'Cette adresse email est déjà utilisée.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'cin.unique'         => 'Ce numéro CIN est déjà utilisé.',
            'date_of_birth.before' => 'La date de naissance doit être dans le passé.',
        ]);

        // 1. Créer le compte utilisateur
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'role'      => 'patient',
            'is_active' => true,
        ]);

        // 2. Créer le dossier patient lié
        Patient::create([
            'user_id'       => $user->id,
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'date_of_birth' => $request->date_of_birth,
            'gender'        => $request->gender,
            'cin'           => $request->cin ?: null,
            'address'       => $request->address ?: null,
            'blood_type'    => $request->blood_type ?: null,
        ]);

        // 3. Connexion automatique
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('patient.dashboard')
            ->with('success', '🎉 Bienvenue ' . $user->name . ' ! Votre compte patient a été créé avec succès.');
    }
}
