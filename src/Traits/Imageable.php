<?php

namespace Sasaya\LaravelImageable\Traits;

use Sasaya\LaravelImageable\Image;

trait Imageable
{
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
