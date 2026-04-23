<?php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['appointments', 'consultations'])
            ->orderBy('role')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'password'   => ['required', Password::min(8)],
            'role'       => 'required|in:admin,medecin,secretaire,patient',
            'phone'      => 'nullable|string',
            'speciality' => 'nullable|string|required_if:role,medecin',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|in:admin,medecin,secretaire,patient',
            'phone'      => 'nullable|string',
            'speciality' => 'nullable|string',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => Password::min(8)]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success',
            $user->is_active ? 'Compte activé.' : 'Compte désactivé.'
        );
    }
}
