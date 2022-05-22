<?php

namespace Controllers\App;

use Controllers\Controller;
use Helpers\Auth;
use Models\Link;
use Models\User;

class AppController extends Controller{
    
    public static function home() {
        $auth_user = Auth::check() ? Auth::user() : null;
        $user = User::find(1);
        $users = User::all();

        return response(200, 'Welcome to link shortener app!', [
            'auth_user' => $auth_user ? $auth_user->toArray() : null,
            'user' => $user->toArray(),
            'users' => $users,
            'useful links' => [
                'auth' => [
                    'login' => [
                        'url' => url('auth/login'),
                        'method' => 'POST|GET'
                    ],
                    'register' => [
                        'url' => url('auth/register'),
                        'method' => 'POST|GET'
                    ],
                ],
                'user links' => [
                    'url' => url('links'),
                    'method' => 'GET'
                ],
                'add new link' => [
                    'url' => url('link'),
                    'method' => 'PUT'
                ],
                'link info' => [
                    'url' => url('link/{id}'),
                    'method' => 'GET'
                ],
                'update link' => [
                    'url' => url('link/{id}'),
                    'method' => 'POST'
                ],
                'delete link' => [
                    'url' => url('link/{id}'),
                    'method' => 'DELETE'
                ],
                'profile' => [
                    'url' => url('profile'),
                    'method' => 'GET'
                ],
                'edit profile' => [
                    'url' => url('profile'),
                    'method' => 'Post'
                ],
            ]
        ]);
    }

    public static function link($code) {
        $redirect_url = '';

        // read cache file
        $cache_file = root_path('caches/links.json');
    
        if (file_exists($cache_file)) {
            $json = file_get_contents($cache_file);
            $links = json_decode($json, true);
            if (key_exists($code, $links))
                $redirect_url = $links[$code];
        }

        if (!$redirect_url) {
            $link = Link::findByCode($code);
            if ($link)
                $redirect_url = $link->toArray()['full_redirect_url'];
        }

        if ($redirect_url)
            return response(200, 'We will redirect you to this link later!', [
                'redirect_url' => $redirect_url
            ]);
        else
            return response(404, 'Url not found.');
    }
}