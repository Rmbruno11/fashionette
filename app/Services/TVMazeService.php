<?php

namespace App\Services;

class TVMazeService implements TVShowsInfoService
{

    /**
     * Queries TV Maze to get TVShows information.
     * 
     * @param string $query Search query
     * 
     * @return array List of TVShows that matched the query.
     */
    public function getTVShows(string $query) : array 
    {
        return ["data" => [
            "title" => $query,
        ]];
    }
}