<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PreRecommendation {
    public function __construct()
    {
        $this->mappingTagsNCountries();
        $this->updateDocumentsInfo();
        $this->createTermFrequencyMatrix();
        $this->initUserProfile();
        $this->createTagsIDFMatrix();
    }

    public function mappingTagsNCountries()
    {
        $artists_id = DB::collection('artists')
            ->select('_id')
            ->get();
        foreach ($artists_id as $id) {
            $artist = Artist::find($id['_id']);
            $tags = $this->getArrayFromString($artist->tags);
            $artist->tags = $tags;
            $artist->tags_count = count($tags);
            if($artist->country != null) {
                $countries = $this->getArrayFromString($artist->country);
                $artist->country = $countries;
            }
            $artist->save();
        }
    }

    public function updateDocumentsInfo()
    {
        $all_tags = [];
        $artists = DB::collection('artists')->select('tags')->get();
        foreach ($artists as $artist) {
            $tags = $artist['tags'];
            foreach ($tags as $tag) {
                if (!in_array($tag, $all_tags)) {
                    array_push($all_tags, $tag);
                }
            }
        }

        DB::collection('documents_info')
            ->where('key', 'tags_count')
            ->update([
                'key' => 'tags_count',
                'value' => count($all_tags)
            ], ['upsert' => true]);

        DB::collection('documents_info')
            ->where('key', 'tags')
            ->update([
                'key' => 'tags',
                'value' => $all_tags
            ], ['upsert' => true]);

        DB::collection('documents_info')
            ->where('key', 'artists_count')
            ->update([
                'key' => 'artists_count',
                'value' => count($artists)
            ], ['upsert' => true]);
    }

    public function createTermFrequencyMatrix() {
        $artists_id = DB::collection('artists')
            ->select('_id')
            ->get();
        foreach ($artists_id as $id) {
            $all_tags = DB::collection('documents_info')
                ->select('value')
                ->where('key', 'tags')
                ->first();
            $all_tags = $all_tags['value'];

            $artist = Artist::find($id['_id']);
            $artist_tags = $artist->tags;
            $properties = [];

            $normalize_value = 1/sqrt(count($artist_tags));

            foreach ($all_tags as $tag) {
                array_push($properties, [
                    'tag' => $tag,
                    'value' => (in_array($tag, $artist_tags)) ? $normalize_value : 0,
                ]);
            }

            DB::collection('artists_tf_matrix')
                ->insert([
                    'artist_id' => $id['_id'],
                    'artist_name' => $artist->name,
                    'properties' => $properties,
                ]);
        }
    }

    public function initUserProfile() {
        $artists_id = DB::collection('artists')
            ->select('_id')
            ->get();

        $preference = [];
        foreach ($artists_id as $id) {
            array_push($preference, [
                'artist_id' => $id['_id'],
                'rating' => 0
            ]);
        }

        $all_tags = DB::collection('documents_info')
            ->select('value')
            ->where('key', 'tags')
            ->first();
        $all_tags = $all_tags['value'];
        $user_profile = [];
        foreach ($all_tags as $tag) {
            array_push($user_profile, [
                'tag' => $tag,
                'value' => 0
            ]);
        }

        $prediction = [];
        foreach ($artists_id as $id) {
            array_push($prediction, [
                'artist_id' => $id['_id'],
                'probability' => 0
            ]);
        }

        $user = new User;
        $user->name = "Budi";
        $user->preference = $preference;
        $user->user_profile = $user_profile;
        $user->prediction = $prediction;
        $user->save();
    }

    public function createTagsIDFMatrix()
    {
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

        $index = 0;
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
                    'index' => $index,
                    'tag' => $tag,
                    'df' => $df,
                    'idf' => log10($artists_count/$df)
                ]);
            $index += 1;
        }
    }

    // UTILITY FUNCTION

    private function getArrayFromString($raw_string)
    {
        $array_string = explode(';', $raw_string);
        $new_array_string = [];
        foreach ($array_string as $string) {
            $formatted_string = strtolower(trim($string));
            array_push($new_array_string, $formatted_string);
        }
        return $new_array_string;
    }
}
