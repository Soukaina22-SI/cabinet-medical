<?php
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est désactivé.']);
            }

            return redirect()->intended($this->redirectTo($user->role));
        }

        return back()->withErrors([
            'email' => 'Les informations d\'identification ne correspondent pas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectTo(string $role): string
    {
        return match($role) {
            'admin'      => route('admin.dashboard'),
            'medecin'    => route('doctor.dashboard'),
            'secretaire' => route('secretary.dashboard'),
            'patient'    => route('patient.dashboard'),
            default      => '/',
        };
    }
}
