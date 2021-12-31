<?php

namespace App\Services;

use App\Models\Artist;
use Illuminate\Support\Facades\DB;


class DataPreprocessing {
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

        // TODO:
        // 1. add updated_at property

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
