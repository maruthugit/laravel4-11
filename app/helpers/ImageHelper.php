<?php

namespace Helper;

use \Config;
use \URL;

trait ImageHelper {

    public static function getServer()
    {
        $server = Config::get('image.server');

        if (empty($server)) {
            $server = URL::to('/');
        }

        return rtrim($server, '/');
    }

    public static function link($path)
    {
        return static::getServer().'/'.ltrim($path, '/');
    }

}
