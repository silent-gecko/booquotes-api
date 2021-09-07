<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Models\Author;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthorBookController extends Controller
{
    public function show(string $uuid)
    {
        if (!Str::isUuid($uuid)) {
            throw new BadRequestHttpException('Invalid id supplied.');
        }

        return new BookCollection(Author::findOrFail($uuid)->books);
    }
}