<?php

use App\Services\TVShowsInfoService;

class TVShowTitlesControllerTest extends TestCase
{
    private $tvshows_service;

    public function setUp(): void
    {
        parent::setUp();
        $this->tvshows_service = Mockery::mock(TVShowsInfoService::class)->makePartial();
        $this->app->instance(TVShowsInfoService::class, $this->tvshows_service);
    }

    /**
     * Verifies that when the query is valid, but no results are found, a proper response is returned.
     */
    public function testQueryTVShowsNoResults()
    {
        $this->tvshows_service->shouldReceive('getTVShows')
            ->once()
            ->with('Batman')
            ->andReturn([]);

        $this->json('GET', '/?q=Batman')
            ->seeJson([
                'status' => 200,
                'data' => []
            ]);
    }

    /**
     * Verifies that when the query is valid and found one result, a proper 
     * response is returned.
     */
    public function testQueryTVShowsOneResult()
    {
        $this->tvshows_service->shouldReceive('getTVShows')
            ->once()
            ->with('Superman')
            ->andReturn([
                [
                    'title' => 'Superman',
                    'language' => 'English',
                    'network' => 'NBC',
                    'summary' => 'An alien boy has superpowers.'
                ]                
            ]);

        $this->json('GET', '/?q=Superman')
            ->seeJson([
                'status' => 200,
                'data' => [[
                    'title' => 'Superman',
                    'language' => 'English',
                    'network' => 'NBC',
                    'summary' => 'An alien boy has superpowers.'
                ]]
            ]);
    }

    /**
     * Verifies that when the query is valid and found several results,
     * a proper response is returned.
     */
    public function testQueryTVShowsMultipleResults()
    {
        $this->tvshows_service->shouldReceive('getTVShows')
            ->once()
            ->with('The Office')
            ->andReturn([
                [
                    'title' => 'The Office',
                    'language' => 'English',
                    'network' => 'NBC',
                    'summary' => 'The Office, american version.'
                ],
                [
                    'title' => 'The Office',
                    'language' => 'English',
                    'network' => 'BBC 2',
                    'summary' => 'The Office, the original british series.'
                ]
            ]);

        $this->json('GET', '/?q=The Office')
            ->seeJson([
                'status' => 200,
                'data' => [
                    [
                        'title' => 'The Office',
                        'language' => 'English',
                        'network' => 'NBC',
                        'summary' => 'The Office, american version.'
                    ],
                    [
                        'title' => 'The Office',
                        'language' => 'English',
                        'network' => 'BBC 2',
                        'summary' => 'The Office, the original british series.'
                    ]
                ]
            ]);
    }

    /**
     * Verifies that when the query ('q') parameter is omitted, we get a 400 error.
     */
    public function testQueryTVShowsWithoutQuery()
    {
        $this->json('GET', '/')
            ->seeJson([
                'status' => 400,
                'error' => [
                    'message' => 'The given data was invalid.'
                ]
            ]);
    }
}