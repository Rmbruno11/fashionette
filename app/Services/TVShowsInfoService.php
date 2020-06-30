<?php

namespace App\Services;

interface TVShowsInfoService
{
    /**
     * Queries a service to get TVShows information.
     * @param string $query Search query to use.
     * 
     * @return array List of movies that matched the query.
     */
    public function getMovies(string $query) : array;
}
