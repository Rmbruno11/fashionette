<?php

namespace App\Http\Controllers;

use App\Services\TVShowInfoService;
use Illuminate\Http\Request;

class TVShowTitlesController extends Controller
{
    private $tvshowsService;
     
    /**
     * Retrieves 
     * Constructs the controller.
     * 
     * @param TVShowInfoService $tvshowsService Instance of the service that 
     * provides TVShows information.
     */
    public function __construct(TVShowInfoService $tvshowsService)
    {
        $this->tvshowsService = $tvshowsService;
    }

    /**
     * Retrieves movies that match the given query.
     */
    public function get(Request $request) {
        
        // Make sure that the query is provided
        $this->validate($request, [
            'q' => 'required'
        ]);

        // Get desired query
        $query = $request->input('q');
        
        // Call the service
        $this->tvshowsService->getTVShows($query);
        $result = $this->tvshowsService->getTVShows($query);

        // Return response
        return response()->json(['status' => 200, 'data' => $result]);
    }
}