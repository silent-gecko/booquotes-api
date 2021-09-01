<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
        'name', 'bio', 'year_of_birth', 'year_of_death'
    ];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10;


    /**
     * Get books for the author.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Get all quotes of the author
     */
    public function quotes()
    {
        return $this->hasManyThrough(Quote::class, Book::class);
    }

    public function getSelfLinkAttribute(): string
    {
        return route('v1.author.get', ['uuid' => $this->id]);
    }

    public function getBooksLinkAttribute(): string
    {
        // TODO: create link pattern
        return 'books-link';
    }

    public function getQuotesLinkAttribute(): string
    {
        // TODO: create link pattern
        return 'quotes-link';
    }
}