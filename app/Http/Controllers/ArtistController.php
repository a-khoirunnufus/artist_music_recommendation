<?php

namespace App\Http\Controllers;

use App\Services\DataPreprocessing;
use Illuminate\Http\Request;
use App\Models\Artist;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ArtistController extends Controller
{
    public function index()
    {
        ini_set('max_execution_time', '3600');

        $all_tags = DB::collection('documents_info')
            ->select('value')
            ->where('key', 'tags')
            ->first();
        $all_tags = $all_tags['value'];

        $artists_count = DB::collection('documents_info')
            ->select('value')
            ->where('key', 'artists_count')
            ->first();
        $artists_count = $artists_count['value'];

        $artists_id = DB::collection('artists')
            ->select('_id')
            ->get();

        foreach ($all_tags as $tag) {
            $df = 0;
            foreach ($artists_id as $id) {
                $artist = Artist::find($id['_id']);
                if (in_array($tag, $artist->tags)) {
                    $df += 1;
                }
            }
            DB::collection('tags_idf_matrix')
                ->insert([
                    $tag => log10($artists_count/$df)
                ]);
        }
    }

}
