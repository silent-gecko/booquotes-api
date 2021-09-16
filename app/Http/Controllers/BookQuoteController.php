<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuoteCollection;
use App\Models\Book;
use Illuminate\Http\Request;

class BookQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    public function show(Request $request, string $uuid)
    {
        return new QuoteCollection(Book::findOrFail($uuid)
            ->quotes()
            ->sorted($request, ['created_at' => 'desc'])
            ->paginate()
            ->withQueryString());
    }
}