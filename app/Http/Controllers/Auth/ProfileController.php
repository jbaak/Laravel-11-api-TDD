<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;

class ProfileController extends Controller
{
    public function update(UserRequest $request) {

        auth()->user()->update($request->validated());
        $user = UserResource::make(auth()->user()->fresh());
        return jsonResponse(compact('user'));
    }
}
