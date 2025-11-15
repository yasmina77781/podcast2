<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Models\Episode;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Rechercher des podcasts par titre, genre ou animateur
     */
    public function searchPodcasts(Request $request)
    {
        // Récupérer les paramètres de recherche
        $title = $request->input('title');
        $genre = $request->input('genre');
        $animateur = $request->input('animateur');
        
        // Commencer la requête
        $query = Podcast::with('user');
        
        // Filtrer par titre si présent
        if ($title) {
            $query->where('title', 'LIKE', '%' . $title . '%');
        }
        
        // Filtrer par genre si présent
        if ($genre) {
            $query->where('genre', 'LIKE', '%' . $genre . '%');
        }
        
        // Filtrer par animateur (nom ou prénom) si présent
        if ($animateur) {
            $query->whereHas('user', function($q) use ($animateur) {
                $q->where('first_name', 'LIKE', '%' . $animateur . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $animateur . '%');
            });
        }
        
        // Récupérer les résultats
        $podcasts = $query->get();
        
        return [
            'podcasts' => $podcasts,
            'count' => $podcasts->count()
        ];
    }

    /**
     * Rechercher des épisodes par titre, podcast ou date
     */
    public function searchEpisodes(Request $request)
    {
        // Récupérer les paramètres de recherche
        $title = $request->input('title');
        $podcast = $request->input('podcast');
        $date = $request->input('date');
        
        // Commencer la requête
        $query = Episode::with('podcast');
        
        // Filtrer par titre si présent
        if ($title) {
            $query->where('title', 'LIKE', '%' . $title . '%');
        }
        
        // Filtrer par nom du podcast si présent
        if ($podcast) {
            $query->whereHas('podcast', function($q) use ($podcast) {
                $q->where('title', 'LIKE', '%' . $podcast . '%');
            });
        }
        
        // Filtrer par date de publication si présent
        if ($date) {
            $query->whereDate('published_at', $date);
        }
        
        // Récupérer les résultats
        $episodes = $query->get();
        
        return [
            'episodes' => $episodes,
            'count' => $episodes->count()
        ];
    }
}
