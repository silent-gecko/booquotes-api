<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    public function show(Request $request, string $uuid)
    {
        return new BookCollection(Author::findOrFail($uuid)
            ->books()
            ->sorted($request, ['sort_index' => 'asc'])
            ->paginate()
            ->withQueryString());
    }
}