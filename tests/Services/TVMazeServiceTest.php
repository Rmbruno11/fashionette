<?php

use App\Services\TVMazeService;
use App\Exceptions\InvalidResponseException;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

class TVMazzeServiceTest extends TestCase
{
    private $handler;
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = HandlerStack::create();
        $this->client = new Client(['handler' => $this->handler]);
        $this->service = Mockery::mock(
            TVMazeService::class, [$this->client])->makePartial();
    }

    /**
     * Sets the simulated response from TV Maze.
     * 
     * @param Response $response This will simulate TV Maze's response. 
     */
    private function setMockedResponse(Response $response)
    {
        $mock = new MockHandler([$response]);
        $this->handler->setHandler($mock);
    }

    /**
     * Verifies that when we have a valid response from TV Maze, we get a valid
     * result.
     */
    public function testValidResponse()
    {
        // Prepare simulated response
        $body = [
            [
                'show' => [
                    'name' => 'Batman',
                    'language' => 'English',
                    'summary' => 'I am Batman.',
                    'network' => [ 'name' => 'BBC' ]
                ]
            ],
            [
                'show' => [
                    'name' => 'Batman Returns',
                    'language' => 'English',
                    'summary' => 'I am Batman, the Sequel.',
                    'network' => [ 'name' => 'BBC' ]
                ]
            ],
            [
                'show' => [
                    'name' => 'batman',
                    'language' => 'English',
                    'summary' => 'I am Batman, but incorrect case.',
                    'network' => [ 'name' => 'NBC' ]
                ]
            ]

        ];
        $this->setMockedResponse(new Response(200, [], json_encode($body)));

        // Call the service
        $result = $this->service->getTVShows('Batman');
        $this->assertEquals(
            [[
                'title' => 'Batman',
                'language' => 'English',
                'summary' => 'I am Batman.',
                'network' => 'BBC'
            ]],
            $result
        );
    }


    /**
     * Verifies that when we have a valid response with multiple results from TV Maze,
     * we get a valid result.
     */
    public function testValidMultipleResponses()
    {
        // Prepare simulated response
        $body = [
            [
                'show' => [
                    'name' => 'Batman',
                    'language' => 'English',
                    'summary' => 'I am Batman.',
                    'network' => [ 'name' => 'BBC' ]
                ]
            ],
            [
                'show' => [
                    'name' => 'Batman',
                    'language' => 'Dutch',
                    'summary' => 'I am Batman, but from another country. Still counts.',
                    'network' => [ 'name' => 'Dutch TV' ]
                ]
            ]
        ];
        $this->setMockedResponse(new Response(200, [], json_encode($body)));

        // Call the service
        $result = $this->service->getTVShows('Batman');
        $this->assertEquals(
            [[
                'title' => 'Batman',
                'language' => 'English',
                'summary' => 'I am Batman.',
                'network' => 'BBC'
            ],
            [
                'title' => 'Batman',
                'language' => 'Dutch',
                'summary' => 'I am Batman, but from another country. Still counts.',
                'network' => 'Dutch TV'
            ]],
            $result
        );
    }

    /**
     * Verifies that when we get an invalid response from TV Maze, we error properly.
     */
    public function testInvalidResponse()
    {
        $this->setMockedResponse(new Response(200, [], 'this is not valid json'));

        $this->expectException(InvalidResponseException::class);
        $result = $this->service->getTVShows('Batman');
    }

    /**
     * Verifies that when there are missing values, we output what we can.
     */
    public function testMissingLanguage()
    {
        $body = [
            [
                'show' => [
                    'name' => 'Batman',
                    'summary' => 'I am Batman.',
                    'network' => [ 'name' => 'BBC' ]
                ]
            ]
        ];
        $this->setMockedResponse(new Response(200, [], json_encode($body)));

        $result = $this->service->getTVShows('Batman');
        $this->assertEquals(
            [[
                'title' => 'Batman',
                'language' => null,
                'summary' => 'I am Batman.',
                'network' => 'BBC'
            ]],
            $result
        );
    }

    /**
     * Verifies that when there are missing values, we output what we can.
     */
    public function testMissingNetwork()
    {
        $body = [
            [
                'show' => [
                    'name' => 'Batman',
                    'summary' => 'I am Batman.',
                ]
            ]
        ];
        $this->setMockedResponse(new Response(200, [], json_encode($body)));

        $result = $this->service->getTVShows('Batman');
        $this->assertEquals(
            [[
                'title' => 'Batman',
                'language' => null,
                'summary' => 'I am Batman.',
                'network' => null
            ]],
            $result
        );
    }


    /**
     * Verifies that when there are missing values, we output what we can.
     */
    public function testMissingShow()
    {
        $body = [
            [
                'whatever' => [
                    'name' => 'Batman',
                    'summary' => 'I am Batman.',
                ]
            ]
        ];
        $this->setMockedResponse(new Response(200, [], json_encode($body)));

        $result = $this->service->getTVShows('Batman');
        $this->assertEquals(
            [],
            $result
        );
    }

    /**
     * Verifies that if the TV Maze API returns a non-successful result, we error
     * accordingly.
     */
    public function testNonOkResult()
    {
        $this->setMockedResponse(new Response(503, [], ''));
        $this->expectException(RequestException::class);
        $result = $this->service->getTVShows('Batman');
    }


}