<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtenir la liste des roles de l'utilisateur
        // $UserRoles = DB::table('roles')->join('role_user', 'role_id', '=', 'roles.id')->where('user_id', '=', Auth::user()->id)->lists('name');
        // // vérifier si cet utilisateur  a le role d'admin
        // $isAdmin = false;
        // foreach ($UserRoles as $role) {
        //     if ($role == 'admin') {
        //         $isAdmin = true;
        //     }
        // }
        // if (! $isAdmin) {
        //     if ($request->ajax()) {
        //         return response('Unauthorized.', 401);
        //     } else {
        //         return redirect()->back(); //todo h peut-etre une fenetre modale pour dire acces refusé ici...
        //     }
        // }

        return $next($request);
    }
}
