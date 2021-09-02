<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorCollection;

class AuthorController extends Controller
{
    public function index()
    {
        return new AuthorCollection(Author::orderBy('sort_index')->paginate());
    }

    public function show(string $uuid)
    {
        return new AuthorResource(Author::find($uuid));
    }
}