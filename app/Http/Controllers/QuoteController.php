<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Models\Quote;
use App\Http\Resources\QuoteCollection;
use App\Http\Resources\QuoteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show', 'update', 'destroy']]);
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

    /**
     * @param QuoteRequest $request
     *
     * @return mixed
     */
    public function store(QuoteRequest $request)
    {
        $quote = Quote::create($request->validated());

        return response()->jsonCreated($quote->id);
    }

    /**
     * @param QuoteRequest $request
     * @param string       $uuid
     *
     * @return Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function update(QuoteRequest $request, string $uuid)
    {
        $quote = Quote::findOrFail($uuid);

        $quote->update($request->validated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function destroy(QuoteRequest $request, string $uuid)
    {
        $quote = Quote::findOrFail($uuid);

        $quote->delete();

        return response('', Response::HTTP_NO_CONTENT);
    }
}