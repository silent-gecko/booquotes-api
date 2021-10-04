<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    /**
     * @return BookCollection
     */
    public function index(Request $request)
    {
        return new BookCollection(Book::sorted($request, ['sort_index' => 'asc'])->paginate()->withQueryString());
    }

    /**
     * @param string $uuid
     *
     * @return BookResource
     */
    public function show(string $uuid)
    {
        return new BookResource(Book::findOrFail($uuid));
    }


}