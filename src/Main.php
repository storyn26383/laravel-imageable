<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class Main
{
    public function foo()
    {
        return Cache::get('foo');
    }
}
