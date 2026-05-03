<?php
// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Support: role:admin,medecin,secretaire  (virgule dans un seul argument)
        //      et: role:admin middleware:medecin   (arguments séparés)
        $allowed = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowed[] = trim($r);
            }
        }

        if (!in_array($userRole, $allowed)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}