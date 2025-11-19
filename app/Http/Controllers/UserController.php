<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;


class UserController extends Controller
{
    /**
     * Lister tous les users (Admin only)
     */
    public function index()
    {
        if (Gate::denies('isAdmin')) {
    return ['message' => 'Non autorisé. Accès réservé aux administrateurs.'];
}
        
        $users = User::with('podcasts')->get();
        
        return ['users' => $users];
    }

    /**
     * Créer un user avec rôle (Admin only)
     */
    public function store(Request $request)
    {
       if (Gate::denies('isAdmin')) {
    return ['message' => 'Non autorisé. Accès réservé aux administrateurs.'];
}
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            
        ]);
        
        $user = User::create($validated);
        
        return [
            'message' => 'Utilisateur créé avec succès',
            'user' => $user
        ];
    }

    /**
     * Afficher un user (Admin only)
     */
    public function show($id)
    {
       if (Gate::denies('isAdmin')) {
    return ['message' => 'Non autorisé. Accès réservé aux administrateurs.'];
}
        
        $user = User::with('podcasts')->find($id);
        
        if (!$user) {
            return ['message' => 'Utilisateur non trouvé'];
        }
        
        return ['user' => $user];
    }

    /**
     * Modifier un user (Admin only)
     */
    public function update(Request $request, $id)
    {
       if (Gate::denies('isAdmin')) {
    return ['message' => 'Non autorisé. Accès réservé aux administrateurs.'];
}
        
        $user = User::find($id);
        
        if (!$user) {
            return ['message' => 'Utilisateur non trouvé'];
        }
        
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:user,animateur,admin'
        ]);
        
        $user->update($validated);
        
        return [
            'message' => 'Utilisateur modifié avec succès',
            'user' => $user
        ];
    }

    /**
     * Supprimer un user (Admin only)
     */
    public function destroy($id)
    {
        if (Gate::denies('isAdmin')) {
    return ['message' => 'Non autorisé. Accès réservé aux administrateurs.'];
}
        
        $user = User::find($id);
        
        if (!$user) {
            return ['message' => 'Utilisateur non trouvé'];
        }
        
        // Empêcher l'admin de se supprimer lui-même
        if (Gate::denies('isAdmin')) {
    return ['message' => 'Non autorisé. Accès réservé aux administrateurs.'];
}
        
        $user->delete();
        
        return ['message' => 'Utilisateur supprimé avec succès'];
    }
}