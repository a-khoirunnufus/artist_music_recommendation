<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Artist extends Model
{
    protected $collection = 'artists';
}
