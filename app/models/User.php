<?php

namespace Models;

use Database\Database;

class User extends Model {

    public static $table = 'users';

    public static $fields = ['id', 'name', 'username', 'password'];

    public static $hidden = ['password'];

    public $id, $name, $username, $password;

    public static function findByUsername(string $username)
    {
        $collection = static::findBySql("SELECT * FROM " . static::$table . " WHERE username='{$username}' LIMIT 1");
        return !empty($collection) ? $collection->shift() : false;
    }

    public function links()
    {
        if ($this->id)
            return Link::findBySql("SELECT * FROM " . Link::$table . " WHERE user_id = " . $this->id);
        else
            return [];
    }

    public function link($id = 0)
    {
        if ($this->id) {
            $links = Link::findBySql("SELECT * FROM " . Link::$table . " WHERE user_id = " . $this->id . " AND id = " . $id);
            return $links ? $links->shift() : null;
        } else
            return [];
    }

    public static function usernameExist($username) 
    {
        $sql = "SELECT id FROM " . static::$table . " WHERE username = '{$username}'";
        return db()->count($sql);
    }
}