<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Podcast;
use App\Models\User;
class PodcastController extends Controller
{
    /**
     * Lister tous les podcasts
     */
    public function index()
    {
        $podcasts = Podcast::with('user')->get();
        
        return ['podcasts' => $podcasts];
    }

    /**
     * Créer un podcast
     */
   public function store(Request $request)
{
    if (!in_array(auth()->user()->role, ['animateur', 'admin'])) {
        return ['message' => 'Non autorisé. Seuls les animateurs et admins peuvent créer des podcasts.'];
    }
    
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'genre' => 'required|string|max:255'
    ]);
    
    // Upload de l'image sur Cloudinary si présente
    $imageUrl = null;
    if ($request->hasFile('image')) {
        $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
        $imageUrl = $uploadedFileUrl;
    }
    
    // Créer le podcast
    $podcast = auth()->user()->podcasts()->create([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'image_url' => $imageUrl,
        'genre' => $validated['genre']
    ]);
    
    return [
        'message' => 'Podcast créé avec succès',
        'podcast' => $podcast
    ];
}

    /**
     * Afficher un podcast
     */
    public function show($id)
    {
        $podcast = Podcast::with('user', 'episodes')->find($id);
        
        if (!$podcast) {
            return ['message' => 'Podcast non trouvé'];
        }
        
        return ['podcast' => $podcast];
    }

    /**
     * Modifier un podcast
     */
   public function update(Request $request, $id)
{
    $podcast = Podcast::find($id);
    
    if (!$podcast) {
        return ['message' => 'Podcast non trouvé'];
    }
    
    $user = auth()->user();
    if ($user->role !== 'admin' && $podcast->user_id !== $user->id) {
        return ['message' => 'Non autorisé. Vous ne pouvez modifier que vos propres podcasts.'];
    }
    
    $validated = $request->validate([
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'genre' => 'sometimes|string|max:255'
    ]);
    
    // Upload nouvelle image si présente
    if ($request->hasFile('image')) {
        $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
        $validated['image_url'] = $uploadedFileUrl;
    }
    
    $podcast->update($validated);
    
    return [
        'message' => 'Podcast modifié avec succès',
        'podcast' => $podcast
    ];
}

    /**
     * Supprimer un podcast
     */
    public function destroy($id)
    {
        $podcast = Podcast::find($id);
        
        if (!$podcast) {
            return ['message' => 'Podcast non trouvé'];
        }
        
        $user = auth()->user();
        if ($user->role !== 'admin' && $podcast->user_id !== $user->id) {
            return ['message' => 'Non autorisé. Vous ne pouvez supprimer que vos propres podcasts.'];
        }
        
        $podcast->delete();
        
        return ['message' => 'Podcast supprimé avec succès'];
    }
}