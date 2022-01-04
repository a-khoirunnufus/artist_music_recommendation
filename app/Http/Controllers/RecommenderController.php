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
    }

    public function index(Request $request)
    {
        // init session for first time
        $select_count = $request->session()->get('select_count');
        if (!boolval($select_count)) {
            $request->session()->put('select_count', '0');
        }

        // if count <= 5 then
        // generate random artist
        if ($select_count <= 5) {
            return view('choose');
        }

        // else
        // show recommendation result
        else {
            return view('result');
        }
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

    public function getRecommendation(Request $request)
    {
        $recommendation_list = (new Recommender)->recommend();
        dd($recommendation_list);
        return 'recommendation list';
    }

}
