<?php

namespace Controllers\App;

use Controllers\Controller;
use Helpers\Auth;
use Jobs\UpdateLinksCache;
use Models\Link;
use Rakit\Validation\Validator;

class LinksController extends Controller {
    public static function all() {
        if (!Auth::check())
            return response(401, 'You need to authorize first.');

        $user = Auth::user();
        $links = $user->links();

        return response(200, 'User links list.', [
            'links' => $links->toArray(),
        ]);
    }

    public static function show($id) {
        if (!Auth::check())
            return response(401, 'You need to authorize first.');

        $link = Link::find($id);

        if (!$link)
            return response(404, 'Link not found.');
        
        if ($link->user_id != Auth::id())
            return response(403, 'You didn\'t have enough permissions to access this link.');
            
        return response(200, 'User link data.', [
            'link' => $link->toArray()
        ]);
    }

    public static function create() {
        if (!Auth::check())
            return response(401, 'You need to authorize first.');

        $validator = new Validator();

        $validation = $validator->validate(request()->all(), [
            'title'        => 'required|min:3|max:64',
            'redirect_url' => 'required|url|max:256',
            'utm_source'   => 'nullable|max:128',
            'utm_medium'   => 'nullable|max:128',
            'utm_campain'  => 'nullable|max:128',
        ]);

        if ($validation->fails())
            return response(422, 'Inputs are not valid', [
                'errors' => $validation->errors()->all()
            ]);

        $title        = request()->get('title');
        $redirect_url = request()->get('redirect_url');
        $utm_source   = request()->get('utm_source') ?: null;
        $utm_medium   = request()->get('utm_medium') ?: null;
        $utm_campain  = request()->get('utm_campain') ?: null;
        $code         = randomString(8);

        $link = Link::create([
            'title'        => $title,
            'redirect_url' => $redirect_url,
            'code'         => $code,
            'utm_source'   => $utm_source,
            'utm_medium'   => $utm_medium,
            'utm_campain'  => $utm_campain,
            'user_id'      => Auth::id()
        ]);

        UpdateLinksCache::dispatch();

        return response(201, 'New Link created successfully.', [
            'link' => $link->toArray()
        ]);
    }

    public static function update($id) {
        if (!Auth::check())
            return response(401, 'You need to authorize first.');

        $link = Link::find($id);
        if (!$link)
            return response(404, 'Link not found.');

        if ($link->user_id != Auth::id())
            return response(404, 'You didn\'t have enough permissions to edit this link.');

        $validator = new Validator();

        $validation = $validator->validate(request()->all(), [
            'title'        => 'required|min:3|max:64',
            'redirect_url' => 'required|url|max:256',
            'utm_source'   => 'nullable|max:128',
            'utm_medium'   => 'nullable|max:128',
            'utm_campain'  => 'nullable|max:128',
        ]);

        if ($validation->fails())
            return response(422, 'Inputs are not valid', [
                'errors' => $validation->errors()->all()
            ]);

        $title        = request()->get('title');
        $redirect_url = request()->get('redirect_url');
        $utm_source   = request()->get('utm_source') ?: null;
        $utm_medium   = request()->get('utm_medium') ?: null;
        $utm_campain  = request()->get('utm_campain') ?: null;

        $link->update([
            'title'        => $title,
            'redirect_url' => $redirect_url,
            'utm_source'   => $utm_source,
            'utm_medium'   => $utm_medium,
            'utm_campain'  => $utm_campain,
        ]);

        UpdateLinksCache::dispatch();

        return response(201, 'Link updated successfully.', [
            'link' => $link->toArray()
        ]);
    }

    public static function delete($id) {
        if (!Auth::check())
            return response(401, 'You need to authorize first.');

        $link = Link::find($id);
        if (!$link)
            return response(404, 'Link not found.');

        if ($link->user_id != Auth::id())
            return response(404, 'You didn\'t have enough permissions to delete this link.');

        $link->delete();

        UpdateLinksCache::dispatch();

        return response(200, 'Link deleted successfully.');
    }
}