<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorCollection;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthorController extends Controller
{
    public function index()
    {
        return new AuthorCollection(Author::orderBy('sort_index')->paginate());
    }

    public function show(string $uuid)
    {
        if (!Str::isUuid($uuid)) {
            throw new BadRequestHttpException('Invalid id supplied.');
        }

        return new AuthorResource(Author::findOrFail($uuid));
    }
}