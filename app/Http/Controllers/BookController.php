<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BookController extends Controller
{
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
        if (!Str::isUuid($uuid)) {
            throw new BadRequestHttpException('Invalid id supplied.');
        }

        return new BookResource(Book::findOrFail($uuid));
    }
}