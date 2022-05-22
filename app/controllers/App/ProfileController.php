<?php

namespace Controllers\App;

use Controllers\Controller;
use Helpers\Auth;
use Models\User;
use Rakit\Validation\Validator;

class ProfileController extends Controller {
    public static function show() {
        if (!Auth::check())
            return response(401, 'You need to authorize first.');

        $user = Auth::user();
        return response(200, 'User profile.', [
            'user' => $user->toArray(),
        ]);
    }

    public static function update() {
        if (!Auth::check())
            return response(401, 'You need to authorize first.');

        $user = Auth::user();

        $validator = new Validator();

        $validation = $validator->validate(request()->all(), [
            'name' => 'required|min:3|max:64',
            'username' => 'required|min:3|max:64|regex:/^[A-Za-z][A-Za-z0-9]{3,64}$/',
            'password' => 'nullable|min:6|max:64'
        ]);

        if ($validation->fails())
            return response(422, 'Inputs are not valid', [
                'errors' => $validation->errors()->all()
            ]);

        $name     = request()->get('name');
        $username = strtolower(request()->get('username'));
        $password = request()->get('password');

        $updates = [
            'name' => $name,
            'username' => $username,
        ];

        if ($user->username != $username && User::usernameExist($username))
            return response(422, 'Inputs are not valid', [
                'errors' => ['Username already taken.']
            ]);
        
        if ($password)
            $updates['password'] = password_hash($password, PASSWORD_DEFAULT);
        
        $user->update($updates);

        return response(200, 'Profile updated successfully.', [
            'user' => $user->toArray(),
        ]);
    }
}