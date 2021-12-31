<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller
{
    public function index()
    {
        $artist = Artist::find('61ce88b6d3ae708150abd273');

        dd($artist);
    }
}
