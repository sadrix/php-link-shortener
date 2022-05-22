<?php

namespace Controllers\App;

use Controllers\Controller;
use Helpers\Auth;
use Models\Link;
use Models\User;

class AppController extends Controller{
    
    public static function home() {
        return response(200, 'Welcome to link shortener app!', [
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
                'links' => [
                    'user links' => [
                        'url' => url('links'),
                        'method' => 'GET'
                    ],
                    'new' => [
                        'url' => url('links'),
                        'method' => 'POST'
                    ],
                    'show' => [
                        'url' => url('link/{id}'),
                        'method' => 'GET'
                    ],
                    'update' => [
                        'url' => url('link/{id}'),
                        'method' => 'POST'
                    ],
                    'delete' => [
                        'url' => url('link/{id}'),
                        'method' => 'DELETE'
                    ],
                ],
                'profile' => [
                    'show' => [
                        'url' => url('profile'),
                        'method' => 'GET'
                    ],
                    'edit' => [
                        'url' => url('profile'),
                        'method' => 'POST'
                    ],
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