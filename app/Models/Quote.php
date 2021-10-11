<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Extensions\Traits\HasUuid;
use App\Extensions\Traits\Sortable;
use Illuminate\Support\Str;

class Quote extends Model
{
    use HasFactory, HasUuid, Sortable;

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
     * Request parameters and model attributes mapping that are available for sorting.
     *
     * @var string[]
     */
    protected $sortable = [
        'date'   => 'created_at',
        'book'   => 'book.sort_index',
        'author' => 'author.sort_index',
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
        return $this->hasOneThrough(
            Author::class,
            Book::class,
            'id',
            'id',
            'book_id',
            'author_id'
        );
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

    /**
     * @return string
     */
    public function getShortFilenameAttribute()
    {
        $cleanAuthorName = preg_replace('/[\W]+/', '', $this->author->name);
        $cleanQuotePart = Str::words(preg_replace('/[^a-zA-Z ]+/', '', $this->text), 4, 'Etc');
        $shortFilename = Str::snake(config('app.name') . $cleanQuotePart . 'By' . $cleanAuthorName);

        return $shortFilename;
    }
}