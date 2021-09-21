<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Extensions\Traits\Sortable;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasUuid, HasFactory, Sortable;

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
     * Request parameters and model attributes mapping that are available for sorting.
     *
     * @var string[]
     */
    protected $sortable = [
        'title' => 'sort_index',
        'author' => 'author.sort_index',
    ];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['author:id,name'];


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
     * Get link to the book show endpoint
     * @return string
     */
    public function getSelfLinkAttribute(): string
    {
        return route('v1.book.show', ['uuid' => $this->id]);
    }

    /**
     * Get link to author show endpoint
     * @return string
     */
    public function getAuthorLinkAttribute(): string
    {
        return route('v1.author.show', ['uuid' => $this->author_id]);
    }

    /**
     * Get link to quotes list endpoint
     * @return string
     */
    public function getQuotesLinkAttribute(): string
    {
        return route('v1.book.quote.show', ['uuid' => $this->id]);
    }

    /**
     * Set sorting index from title value
     * @param $value
     */
    public function setTitleAttribute($value): void
    {
        $modifiedTitle = Str::title(trim($value));
        $this->attributes['title'] = $modifiedTitle;

        if (stripos($modifiedTitle, 'The ') === 0) {
            $modifiedTitle = Str::replaceFirst('The ', '', $modifiedTitle);
        }
        $sortingValue = Str::lower(Str::remove(' ', $modifiedTitle));

        $this->attributes['sort_index'] = $sortingValue;
    }
}