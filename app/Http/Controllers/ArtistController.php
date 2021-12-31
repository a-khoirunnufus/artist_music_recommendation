<?php

namespace App\Http\Controllers;

use App\Services\DataPreprocessing;
use Illuminate\Http\Request;
use App\Models\Artist;
use Illuminate\Support\Facades\DB;

class ArtistController extends Controller
{
    public function index() {
        (new DataPreprocessing)->createTermFrequencyMatrix();
    }

}
