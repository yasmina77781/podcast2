<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
   
    public function index($podcast_id)
    {
        
        $podcast = Podcast::find($podcast_id);
        
        if (!$podcast) {
            return [
                'message' => 'Podcast non trouvé'
            ];
        }
        
       
        $episodes = $podcast->episodes;
        
        return [
            'episodes' => $episodes
        ];
    }

    
    public function show($id)
    {
        
        $episode = Episode::with('podcast')->find($id);
        
        if (!$episode) {
            return [
                'message' => 'Épisode non trouvé'
            ];
        }
        
        return [
            'episode' => $episode
        ];
    }

    /**
     * Créer un épisode pour un podcast
     */
   public function store(Request $request, $podcast_id)
{
    $podcast = Podcast::find($podcast_id);
    
    if (!$podcast) {
        return ['message' => 'Podcast non trouvé'];
    }
    
    $user = auth()->user();
    if ($user->role !== 'admin' && $podcast->user_id !== $user->id) {
        return ['message' => 'Non autorisé. Vous ne pouvez ajouter des épisodes qu\'à vos propres podcasts.'];
    }
    
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'audio' => 'required|mimes:mp3,wav,ogg|max:10240',
        'duration' => 'nullable|integer',
        'published_at' => 'nullable|date'
    ]);
    
    // Upload du fichier audio sur Cloudinary
    $audioUrl = cloudinary()->upload($request->file('audio')->getRealPath(), [
        'resource_type' => 'video'
    ])->getSecurePath();
    
    // Créer l'épisode
    $episode = $podcast->episodes()->create([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'audio_url' => $audioUrl,
        'duration' => $validated['duration'] ?? null,
        'published_at' => $validated['published_at'] ?? now()
    ]);
    
    return [
        'message' => 'Épisode créé avec succès',
        'episode' => $episode
    ];
}
    
   public function update(Request $request, $id)
{
    $episode = Episode::with('podcast')->find($id);
    
    if (!$episode) {
        return ['message' => 'Épisode non trouvé'];
    }
    
    $user = auth()->user();
    if ($user->role !== 'admin' && $episode->podcast->user_id !== $user->id) {
        return ['message' => 'Non autorisé. Vous ne pouvez modifier que les épisodes de vos propres podcasts.'];
    }
    
    $validated = $request->validate([
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'audio' => 'nullable|mimes:mp3,wav,ogg|max:10240',
        'duration' => 'nullable|integer',
        'published_at' => 'nullable|date'
    ]);
    
    // Upload nouveau audio si présent
    if ($request->hasFile('audio')) {
        $audioUrl = cloudinary()->upload($request->file('audio')->getRealPath(), [
            'resource_type' => 'video'
        ])->getSecurePath();
        $validated['audio_url'] = $audioUrl;
    }
    
    $episode->update($validated);
    
    return [
        'message' => 'Épisode modifié avec succès',
        'episode' => $episode
    ];
}

   
    public function destroy($id)
    {
        
        $episode = Episode::with('podcast')->find($id);
        
        if (!$episode) {
            return[
                'message' => 'Épisode non trouvé'
            ];
        }
        
        // Vérifier les permissions (propriétaire du podcast parent ou admin)
        $user = auth()->user();
        if ($user->role !== 'admin' && $episode->podcast->user_id !== $user->id) {
            return[
                'message' => 'Non autorisé. Vous ne pouvez supprimer que les épisodes de vos propres podcasts.'
            ];
        }
        
        // Supprimer
        $episode->delete();
        
        return [
            'message' => 'Épisode supprimé avec succès'
        ];
    }
}