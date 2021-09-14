<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Models\Author;

class AuthorBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    public function show(string $uuid)
    {
        return new BookCollection(Author::findOrFail($uuid)->books()->paginate());
    }
}