<?php

namespace Controllers\Auth;

use Controllers\Controller;
use Helpers\Auth;
use Models\User;
use Rakit\Validation\Validator;

class AuthController extends Controller {

    public static function login() {
        // check not logged in before
        if (Auth::check()) 
            return response(403, 'You are already logged in.');

        $username = request()->get('username');
        $password = request()->get('password');

        $user = User::findByUsername($username);
        if ($user) {
            if (password_verify($password, $user->password)) {
                Auth::login($user);
                return response(200, 'You have successfully logged in.', null);
            }
        }

        return response(422, 'Username and password do not matched.', [
            'user' => $user->toArray()
        ]);
    }

    public static function register() {
        if (Auth::check())
            return response(403, 'You are already logged in.');

        $validator = new Validator();

        $validation = $validator->validate(request()->all(), [
            'name' => 'required|min:3|max:64',
            'username' => 'required|min:3|max:64|regex:/^[A-Za-z][A-Za-z0-9]{3,64}$/',
            'password' => 'required|min:6|max:64'
        ]);

        if ($validation->fails()) 
            return response(422, 'Inputs are not valid', [
                'errors' => $validation->errors()->all()
            ]);

        $name     = request()->get('name');
        $username = strtolower(request()->get('username'));
        $password = request()->get('password');

        if (User::usernameExist($username))
            return response(422, 'Inputs are not valid', [
                'errors' => ['Username already taken.']
            ]);

        $user = User::create([
            'name' => $name,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
        Auth::login($user);

        return response(200, 'New account registered and logged in successfully.', [
            'user' => $user->toArray(),
        ]);

    }

    public static function logout() {
        Auth::logout();
        return response(200, 'You have successfully logged out.');
    }

}