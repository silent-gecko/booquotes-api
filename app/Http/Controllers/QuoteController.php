<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Http\Resources\QuoteCollection;
use App\Http\Resources\QuoteResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    /**
     * @return QuoteCollection
     */
    public function index(Request $request)
    {
        return new QuoteCollection(Quote::sorted($request, ['created_at' => 'desc'])->paginate()->withQueryString());
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
        return new QuoteResource(Quote::inRandomOrder()->first());
    }
}