<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuoteCollection;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    public function show(Request $request, string $uuid)
    {
        return new QuoteCollection(Author::findOrFail($uuid)
            ->quotes()
            ->sorted($request, ['created_at' => 'desc'])
            ->paginate()
            ->withQueryString());
    }
}