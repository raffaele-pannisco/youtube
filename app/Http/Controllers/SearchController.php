<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function index()
    {
        return view('index');
    }

    public function search(Request $request)
    {

        if ($request->ajax()) {
            $params = [
                'q' => $request->search,
                'type' => 'video',
                'part' => 'snippet',
                'maxResults' => 12
            ];
            
            $search = \Youtube::searchAdvanced($params);
            
            return view('search')->withStore($search); ;
        }
    }
}
