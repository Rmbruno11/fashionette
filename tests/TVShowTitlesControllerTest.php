<?php

use App\Services\MoviesInfoService;

class TVShowTitlesTest extends TestCase
{
    /**
     * Verifies that when we issue a valid query, we get a valid result.
     */
    public function testQueryTVShows() {
        $this->json('GET', '/?q=Batman')
            ->seeJson([
                'status' => 200,
                'input' => 'Batman'
            ]);
    }

    /**
     * Verifies that when the query ('q') parameter is omitted, we get a 400 error.
     */
    public function testQueryMoviesWithoutQuery() {
        $this->json('GET', '/')
            ->seeJson([
                'status' => 400,
                'error' => [
                    'message' => 'The given data was invalid.'
                ]
            ]);
    }
}