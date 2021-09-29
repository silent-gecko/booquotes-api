<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Models\Author;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorCollection;
use App\Rules\Year;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
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

    public function store(StoreAuthorRequest $request)
    {
        $validated = $request->validated();
        $author = Author::create($validated);

        return response()->jsonCreated($author->id);
    }
}