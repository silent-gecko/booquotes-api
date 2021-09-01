<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Author;
use App\Models\Book;
use App\Models\Quote;

/**
 * Seeds full quotes data (quotes themselves + related books data + related authors data)
 * by importing from .csv file.
 */
class QuotesFullDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataLegend = ['author_name', 'birth_year', 'death_year', 'bio', 'book_title', 'description', 'quote'];
        $authorId = '';
        $bookId = '';

        if ($file = fopen(resource_path('dbseed/quotes_db_seed.csv'), 'r')) {
            while (($data = fgetcsv($file)) !== false) {
                $data = array_combine($dataLegend, $data);
                if ($data['author_name']) {
                    $lastNameLetter = mb_strtolower(
                        substr($data['author_name'], strrpos($data['author_name'], ' ') + 1, 1)
                    );
                    $authorId = DB::table('authors')->insertGetId([
                        'name'          => $data['author_name'],
                        'sort_index'    => $lastNameLetter,
                        'year_of_birth' => $data['birth_year'],
                        'year_of_death' => $data['death_year'] ?: null,
                        'bio'           => $data['bio'] ?: null,
                    ]);
                }
                if ($data['book_title']) {
                    $bookId = DB::table('books')->insertGetId([
                        'title' => $data['book_title'],
                        'description' => $data['description'] ?: null,
                        'author_id' => $authorId,
                    ]);
                }
                Quote::create([
                    'text' => $data['quote'],
                    'book_id' => $bookId,
                ]);
            }
            fclose($file);
        }
    }
}
