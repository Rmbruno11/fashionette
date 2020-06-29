<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TVShowTitlesController extends Controller
{
    /**
     * Retrieves 
     */
    public function get(Request $request) {
        
        // Make sure that the query is provided
        $this->validate($request, [
            'q' => 'required'
        ]);

        // Just return what was enterd
        $query = $request->input('q');
        return response()->json(['status' => 200, 'input' => $query]);
    }
}