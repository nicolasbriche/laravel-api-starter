<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     *  Get authenticated user
     *
     *  Retourne les informations de l'utilisateur actuellement authentifié via son token Sanctum.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
