<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::User();
        // 1. Cek apakah user login
        if (!Auth::check()) {
            return redirect('/login');
        }
        /**
         * @var
         */
        // 2. Ambil peran user yang sedang login
        $userRole = $user->role;
        Log::info("User Role: {$userRole}", ['Allowed Roles' => $roles]);
        // 3. Cek apakah peran user ada di daftar peran yang diizinkan ($roles)
        if (!in_array($userRole, $roles)) {
            // Jika tidak memiliki peran yang diizinkan, kembalikan error 403 (Unauthorized)
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}
