<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasUuid, HasFactory;

    const SORT_INDEX_LENGTH = 3;

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
        'name',
        'bio',
        'year_of_birth',
        'year_of_death'
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

    /**
     * Get link to the author detail show endpoint
     * @return string
     */
    public function getSelfLinkAttribute(): string
    {
        return route('v1.author.show', ['uuid' => $this->id]);
    }

    /**
     * Get link to author's books list endpoint
     * @return string
     */
    public function getBooksLinkAttribute(): string
    {
        return route('v1.author.book.show', ['uuid' => $this->id]);
    }

    /**
     * Get link to author's quotes list endpoint
     * @return string
     */
    public function getQuotesLinkAttribute(): string
    {
        return route('v1.author.quote.show', ['uuid' => $this->id]);
    }

    /**
     * Set sorting value from author's full name
     *
     * @param $value
     *
     * @return void
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = trim($value);
        $this->attributes['sort_index'] = mb_strtolower(
            substr($value, strrpos($value, ' ') + 1, self::SORT_INDEX_LENGTH)
        );
    }
}