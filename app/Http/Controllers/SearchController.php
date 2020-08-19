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
        $params = [
            'q' => $request->search,
            'maxResults' => 12
        ];
        
        if ($request->ajax()) {
            $search = \Youtubeapi::searchVideos($params);

            return view('search')->withStore($search);
        }
    }
}
