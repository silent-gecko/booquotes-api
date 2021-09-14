<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    /**
     * @return BookCollection
     */
    public function index()
    {
        return new BookCollection(Book::orderBy('sort_index')->paginate());
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