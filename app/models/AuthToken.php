<?php

namespace Models;

class AuthToken extends Model {

    public static $table = 'auth_tokens';

    public static $fields = ['id', 'user_id', 'token'];

    public $id, $user_id, $token;

    public static function findByToken(string $token = '')
    {
        $collection = self::findBySql("SELECT * FROM " . self::$table . " WHERE token='{$token}' LIMIT 1");
        return $collection->count() ? $collection->shift() : null;
    }

    public function user()
    {
        if ($this->user_id)
            return User::find($this->user_id);
        else
            return null;
    }
}