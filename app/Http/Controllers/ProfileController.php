<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(UserRequest $request) {

        auth()->user()->update($request->validated());
        $user = UserResource::make(auth()->user()->fresh());
        return jsonResponse(compact('user'));
    }
}
