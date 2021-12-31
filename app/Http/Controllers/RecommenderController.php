<?php

namespace App\Http\Controllers;

use App\Services\DataPreprocessing;
use App\Services\Recommender;
use Illuminate\Http\Request;

class RecommenderController extends Controller
{
    public function preprocessing()
    {
        // (new DataPreprocessing)->mappingTagsNCountries();
        // (new DataPreprocessing)->updateDocumentsInfo();
        // (new DataPreprocessing)->createTermFrequencyMatrix();
        // (new DataPreprocessing)->initUserProfile();
        // (new DataPreprocessing)->createTagsIDFMatrix();
    }

    public function index(Request $request)
    {
        $recommender = new Recommender;
        $recommender->generateRecommendation();

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
}
