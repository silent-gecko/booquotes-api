<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Http\Resources\QuoteCollection;
use App\Http\Resources\QuoteResource;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    /**
     * @return QuoteCollection
     */
    public function index()
    {
        return new QuoteCollection(Quote::orderBy('updated_at')->paginate());
    }

    /**
     * @param string $uuid
     *
     * @return QuoteResource
     */
    public function show(string $uuid)
    {
        return new QuoteResource(Quote::findOrFail($uuid));
    }

    /**
     * @return QuoteResource
     */
    public function showRandom()
    {
        return new QuoteResource(Quote::all()->random());
    }
}