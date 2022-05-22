<?php

namespace Jobs;

use Models\Link;

class UpdateLinksCache {

    public static function dispatch()
    {
        $links = Link::all();

        $array = [];
        if ($links->count())
            foreach ($links->toArray() as $link)
                $array[$link['code']] = $link['full_redirect_url'];

        $json = json_encode($array, JSON_PRETTY_PRINT);

        // create/update cache file
        $fp = fopen(root_path('caches/links.json'), 'w');
        fwrite($fp, $json);
        fclose($fp);
    }

}