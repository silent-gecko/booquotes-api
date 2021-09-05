<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Traits\HasUuid;

class Book extends Model
{
    use HasUuid;

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
        'title', 'description', 'author_id'
    ];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10;


    /**
     * Get the author of the book.
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get quotes of the book.
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Set sorting index from title value
     * @param $value
     */
    public function setTitleAttribute($value): void
    {
        $modifiedTitle = trim($value);
        $this->attributes['title'] = $modifiedTitle;

        $modifiedTitle = explode(' ', mb_strtolower($modifiedTitle));
        $sortingLetters = substr($modifiedTitle[0] == 'the' ? $modifiedTitle[1] : $modifiedTitle[0], 0,
            self::SORT_INDEX_LENGTH);
        $this->attributes['sort_index'] = $sortingLetters;
    }
}