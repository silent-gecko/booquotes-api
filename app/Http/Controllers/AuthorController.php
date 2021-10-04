<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Models\Author;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorCollection;
use App\Rules\Year;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show', 'update']]);
    }

    /**
     * @return AuthorCollection
     */
    public function index(Request $request)
    {
        return new AuthorCollection(Author::sorted($request, ['sort_index' => 'asc'])->paginate()->withQueryString());
    }

    /**
     * @param string $uuid
     *
     * @return AuthorResource
     */
    public function show(string $uuid)
    {
        return new AuthorResource(Author::findOrFail($uuid));
    }

    /**
     * @param AuthorRequest $request
     *
     * @return mixed
     */
    public function store(AuthorRequest $request)
    {
        $author = Author::create($request->validated());

        return response()->jsonCreated($author->id);
    }

    public function update(AuthorRequest $request, string $uuid)
    {
        $author = Author::findOrFail($uuid);
        $author->update($request->validated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function destroy(string $uuid)
    {
        $author = Author::findOrFail($uuid);
        if ($author->books->count()) {
            return response()->jsonError(Response::HTTP_CONFLICT,
                'Author can not be deleted: there are related books in collection.');
        }

        $author->delete();

        return response('', Response::HTTP_NO_CONTENT);
    }
}