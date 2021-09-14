<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuoteCollection;
use App\Models\Author;

class AuthorQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    public function show(string $uuid)
    {
        return new QuoteCollection(Author::findOrFail($uuid)->quotes()->paginate());
    }
}