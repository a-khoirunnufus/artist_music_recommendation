<?php

namespace App\Http\Controllers;

use App\Services\PreRecommendation;
use App\Services\Recommender;
use Illuminate\Http\Request;

class RecommenderController extends Controller
{
    public function preRecommendation()
    {
        new PreRecommendation();

        return "success";
    }

    public function index(Request $request)
    {
        // get num of like+unlike
        $recommender = new Recommender();
        $numOfLikeUnlike = $recommender->getNumOfLikes() + $recommender->getNumOfUnlikes();

        $artist = $recommender->getRandomArtist();

        return view('choose', ['artist' => $artist]);
    }

    public function engagement(Request $request)
    {
        $artist_id = $request->artist_id;
        $type = $request->engagement_type;

        if ($type == "like") {
            (new Recommender)->likeArtist($artist_id);
        } else if ($type == "unlike") {
            (new Recommender)->unlikeArtist($artist_id);
        }

        return redirect()->back();
    }

    public function resetPreferences()
    {
        (new Recommender)->resetPreferences();

        return redirect('/');
    }

    public function getRecommendation(Request $request)
    {
        $recommendation_list = (new Recommender)->recommend();

        return view('result', [
            'recommendations' => $recommendation_list
        ]);
    }

}
