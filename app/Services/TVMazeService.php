<?php

namespace App\Services;

use Cache;
use App\Exceptions\InvalidResponseException;
use GuzzleHttp\Client;

class TVMazeService implements TVShowsInfoService
{

    private const TVMAZE_BASE_URL_KEY = 'TVMAZE_BASE_URL';
    private const TVMAZE_CACHE_TTL_KEY = 'TVMAZE_CACHE_TTL'; 
    
    private $client;
    private $base_url;
    private $cache_ttl;

    /**
     * Constructs the TV Maze Service.
     * 
     * @param Client $client Instance of Guzzle HTTP Client.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->base_url = env(self::TVMAZE_BASE_URL_KEY);
        $this->cache_ttl = (int)env(self::TVMAZE_CACHE_TTL_KEY);
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

        // If the result is cached, then we don't need to query TV Maze again
        if (Cache::has($query)) {
            return Cache::get($query);
        }
        
        // Query TV Maze
        $response = $this->client->request('GET', $url);

        // Parse JSON Response
        $body = json_decode($response->getBody(), true);
        if (empty($body)) {
            throw new InvalidResponseException('Invalid response from TV Maze Service');
        }
        
        // Filter results that matched the query
        $result = [];
        foreach($body as $element) {
            $title = $element['show']['name'] ?? null;
            $language = $element['show']['language'] ?? null;
            $summary = $element['show']['summary'] ?? null;
            $network = $element['show']['network']['name'] ?? null;
            
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
        
        // Store results in cache
        Cache::put($query, $result, $this->cache_ttl);
        
        return $result;
    }
}