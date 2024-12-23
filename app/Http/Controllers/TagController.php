<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __invoke(Tag $tag)
    {
        // return view('results', ['jobs' => $tag->jobs]);
        try {

            return response()->json(['status' => 'success', 'jobs' => $tag->jobs]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'error' => $e->getMessage()], 400);
        }
    }
}
