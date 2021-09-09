<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuoteCollection;
use App\Models\Author;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthorQuoteController extends Controller
{
    public function show(string $uuid)
    {
        if (!Str::isUuid($uuid)) {
            throw new BadRequestHttpException('Invalid id supplied.');
        }

        return new QuoteCollection(Author::findOrFail($uuid)->quotes()->paginate());
    }
}