<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    /**
     * @param BookRequest $request
     *
     * @return mixed
     */
    public function store(BookRequest $request)
    {
        $book = Book::create($request->validated());

        return response()->jsonCreated($book->id);
    }

    /**
     * @param BookRequest $request
     * @param string      $uuid
     *
     * @return Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function update(BookRequest $request, string $uuid)
    {
        $book = Book::findOrFail($uuid);

        $book->update($request->validated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $uuid
     *
     * @return Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function destroy(string $uuid)
    {
        $book = Book::findOrFail($uuid);
        if ($book->quotes->count()) {
            return response()->jsonError(Response::HTTP_CONFLICT,
                'Book can not be deleted: there are related quotes in collection.');
        }

        $book->delete();

        return response('', Response::HTTP_NO_CONTENT);
    }
}