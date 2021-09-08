<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'book_id'
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['book:id,title,author_id',];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 20;


    /**
     * Get the book of the quote.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the author of the quote.
     */
    public function author()
    {
        return $this->book->author;
    }

    /**
     * Get link to quote endpoint
     * @return string
     */
    public function getSelfLinkAttribute()
    {
        return route('v1.quote.show', ['uuid' => $this->id]);
    }

    /**
     * Get link to quote book endpoint
     * @return string
     */
    public function getBookLinkAttribute()
    {
        return route('v1.book.show', ['uuid' => $this->book->id]);
    }

    /**
     * Get link to quote author endpoint
     * @return string
     */
    public function getAuthorLinkAttribute()
    {
        return route('v1.author.show', ['uuid' => $this->book->author->id]);
    }
}