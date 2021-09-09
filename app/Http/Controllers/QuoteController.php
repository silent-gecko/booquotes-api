<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Http\Resources\QuoteCollection;
use App\Http\Resources\QuoteResource;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class QuoteController extends Controller
{
    /**
     * @return QuoteCollection
     */
    public function index()
    {
        return new QuoteCollection(Quote::orderBy('updated_at')->paginate());
    }

    public function show(string $uuid)
    {
        if (!Str::isUuid($uuid)) {
            throw new BadRequestHttpException('Invalid id supplied.');
        }

        return new QuoteResource(Quote::findOrFail($uuid));
    }

    public function showRandom()
    {
        return new QuoteResource(Quote::all()->random());
    }
}