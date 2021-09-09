<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuoteCollection;
use App\Models\Book;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BookQuoteController extends Controller
{
    public function show(string $uuid) {
        if (!Str::isUuid($uuid)) {
            throw new BadRequestHttpException('Invalid id supplied.');
        }

        return new QuoteCollection(Book::findOrFail($uuid)->quotes()->paginate());
    }
}