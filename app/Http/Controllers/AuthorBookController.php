<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Models\Author;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthorBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_uuid', ['only' => ['show']]);
    }

    public function show(string $uuid)
    {
        return new BookCollection(Author::findOrFail($uuid)->books()->paginate());
    }
}