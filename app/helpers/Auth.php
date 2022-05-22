<?php

namespace Helpers;

use Models\AuthToken;
use Models\User;
use Rakit\Validation\Validator;

class Auth {

    private static $id = null;

    private static $user = null;

    private static $auth_token = null;

    private static $expire_in = 60 * 60 * 24 * 30; // expire auth token cookie after 1 month

    public static function user()
    {
        return self::$user;
    }

    public static function id()
    {
        return self::$id;
    }

    public static function login(User $user) {
        $auth_token = AuthToken::create([
            'user_id' => $user->id,
            'token' => self::newToken()
        ]);
        setcookie('auth_token', $auth_token->token, time() + self::$expire_in, '/', NULL);
        self::$auth_token = $auth_token;
        self::$user = $user;
        self::$id = $user->id;
    }

    public static function logout() {
        if (self::$auth_token)
            self::$auth_token->delete();

        unset($_COOKIE['auth_token']);
        setcookie('auth_token', null, -1, '/', NULL);
        self::$auth_token = null;
        self::$user = null;
        self::$id = null;
    }

    /**
     * check user has valid auth token and login
     */
    public static function check() 
    {
        if (self::$user)
            return true;

        if (isset($_COOKIE['auth_token'])) {
            $token = $_COOKIE['auth_token'];

            $validator = new Validator();

            $validation = $validator->validate(['token' => $token], [
                'token' => 'required',
            ]);

            if (!$validation->fails()) {
                $auth_token = AuthToken::findByToken($token);
                
                if ($auth_token) {
                    $user = $auth_token->user();

                    if ($user) {
                        self::$id = $user->id;
                        self::$user = $user;
                        return true;
                    }
                }
            } else {
                self::logout();
            }
        }

        return false;
    }

    /**
     * return random 16 digits string
     */
    private static function newToken() : string
    {
        return randomString(16);
    }

}