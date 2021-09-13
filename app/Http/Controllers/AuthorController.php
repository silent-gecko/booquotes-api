<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\BookCollection;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    /**
     * @return AuthorCollection
     */
    public function index()
    {
        return new AuthorCollection(Author::orderBy('sort_index')->paginate());
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
}