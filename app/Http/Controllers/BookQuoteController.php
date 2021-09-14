<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuoteCollection;
use App\Models\Book;

class BookQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    public function show(string $uuid) {
        return new QuoteCollection(Book::findOrFail($uuid)->quotes()->paginate());
    }
}