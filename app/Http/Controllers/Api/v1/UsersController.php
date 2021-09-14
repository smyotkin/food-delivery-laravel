<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsersResource;
use App\Services\UsersService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return UsersResource::collection(
            UsersService::find()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return UsersResource
     * @throws \Throwable
     */
    public function show($id)
    {
        return UsersResource::make(
            UsersService::getOrFail([
                'id' => $id,
            ])
        );
    }
}
