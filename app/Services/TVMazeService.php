<?php

namespace App\Services;

use GuzzleHttp\Client;

class TVMazeService implements TVShowsInfoService
{

    private const TVMAZE_BASE_URL_KEY = 'TVMAZE_BASE_URL';

    private $client;
    private $base_url;

    /**
     * Constructs the TV Maze Service.
     * 
     * @param Client $client Instance of Guzzle HTTP Client.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->base_url = env(self::TVMAZE_BASE_URL_KEY);
        if (empty($this->base_url)) {
            throw new \Exception('Emtpy TV Maze Url');
        }
    }

    /**
     * Queries TV Maze to get TVShows information.
     * 
     * @param string $query Search query
     * 
     * @return array List of TVShows that matched the query.
     */
    public function getTVShows(string $query) : array 
    {
      // Build TV Maze URL
        $url = sprintf("%s/search/shows?q=%s", $this->base_url, $query);

        // Make the request
        $response = $this->client->request('GET', $url);

        // Parse JSON Response
        $body = json_decode($response->getBody(), true);

        // Filter results that matched the query
        $result = [];
        foreach($body as $element) {
            $title = $element['show']['name'];
            $language = $element['show']['language'];
            $summary = $element['show']['summary'];
            $network = $element['show']['network']['name'];

            // If the title is an exact match, then we add it to the result set.
            if ($title === $query) {
                $result[] = [
                    'title' => $title,
                    'language' => $language,
                    'summary' => $summary,
                    'network' => $network
                ];
            }
        }
        return $result;
    }
}