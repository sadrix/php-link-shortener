<?php

namespace Models;

class Link extends Model {

    public static $table = 'links';

    public static $fields = ['id', 'user_id', 'redirect_url', 'code', 'title', 'utm_source', 'utm_medium', 'utm_campain'];

    public static $appends = ['short_link'];

    public $id, $user_id, $redirect_url, $code, $title, $utm_source, $utm_medium, $utm_campain, $short_link;

    public static function findByCode($code) {
        $collection = static::findBySql("SELECT * FROM " . static::$table . " WHERE code='{$code}' LIMIT 1");
        return !empty($collection) ? $collection->shift() : false;
    }

    public function getShortLinkAttribute() {
        $params = [];

        if ($this->utm_source)
            $params['utm_source'] = $this->utm_source;

        if ($this->utm_medium)
            $params['utm_medium'] = $this->utm_medium;

        if ($this->utm_campain)
            $params['utm_campain'] = $this->utm_campain;

        return url($this->code, $params);
    }
}