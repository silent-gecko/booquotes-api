<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Resources\AuthorResource;

class AuthorController extends Controller
{
    public function index()
    {
        return AuthorResource::collection(Author::all());
    }

    public function get(string $uuid)
    {
        return new AuthorResource(Author::find($uuid));
    }
}