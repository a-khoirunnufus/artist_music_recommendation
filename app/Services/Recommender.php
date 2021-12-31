<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Recommender
{
    protected $user;

    public function __construct()
    {
        $user = User::all()->first();
        $this->user = $user;
    }

    public function likeArtist($artist_id)
    {
        $user_preferences = $this->user->preference;
        foreach ($user_preferences as $index => $preference) {
            if ($preference['artist_id'] == $artist_id) {
                $user_preferences[$index]['rating'] = 1;
            }
        }
        $this->user->preference = $user_preferences;
        $this->user->save();
    }

    public function unlikeArtist($artist_id)
    {
        $user_preferences = $this->user->preference;
        foreach ($user_preferences as $index => $preference) {
            if ($preference['artist_id'] == $artist_id) {
                $user_preferences[$index]['rating'] = -1;
            }
        }
        $this->user->preference = $user_preferences;
        $this->user->save();
    }

    public function generateUserProfile()
    {
        // generate user preference vector
        $user_preferences = $this->user->preference;
        $user_preference_vector = [];
        foreach ($user_preferences as $preference) {
            array_push($user_preference_vector, $preference['rating']);
        }

        $user_profile = $this->user->user_profile;

        foreach ($user_profile as $index => $tag) {
            // generate tag vector per loop
            $tag_vector = [];
            $artists = DB::collection('artists_tf_matrix')
                            ->select('properties')
                            ->get();
            foreach ($artists as $artist) {
                $tf_value = $artist['properties'][$index]['value'];
                array_push($tag_vector, $tf_value);
            }

            $dot_product = $this->dotProduct($tag_vector, $user_preference_vector);
            $user_profile[$index]['value'] = $dot_product;
        }

        $this->user->user_profile = $user_profile;
        $this->user->save();
    }

    public function generateRecommendation()
    {
        // generate artist vector
        $artists_vectors = [];
        $artists = DB::collection('artists_tf_matrix')
                        ->select('properties')
                        ->get();
        foreach ($artists as $index => $artist) {
            $artist_tf_vector = [];
            $properties = $artist['properties'];
            foreach ($properties as $property) {
                array_push($artist_tf_vector, $property['value']);
            }
            array_push($artists_vectors, $artist_tf_vector);
        }
        // dd($artists_vectors);

        // generate idf vector
        $idf_vector = [];
        $tags = DB::collection('tags_idf_matrix')
                    ->select('idf')
                    ->get();
        foreach ($tags as $tag) {
            array_push($idf_vector, $tag['idf']);
        }
        // dd($idf_vector);

        // generate user profile vector
        $user_profile_vector = [];
        $tags = DB::collection('users')
                    ->select('user_profile')
                    ->first();
        $tags = $tags['user_profile'];
        foreach ($tags as $tag) {
            array_push($user_profile_vector, $tag['value']);
        }
        dd($user_profile_vector);
    }

    // UTILITY FUNCTION

    function dotProduct($vect_A, $vect_B)
    {
        $n = 3;
        $product = 0;

        for ($i = 0; $i < $n; $i++)
            $product = $product + $vect_A[$i] * $vect_B[$i];
        return $product;
    }

}
