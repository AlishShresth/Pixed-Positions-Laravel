<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * @param str $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        try {

            // dd(request('q'));
            $jobs = Job::with(['employer', 'tags'])->where('title', 'LIKE', '%' . $name . '%')->get();
            // return view('results',['jobs'=>$jobs]);
            return response()->json([
                'status' => 'success',
                'jobs' => $jobs
            ]);
        } catch (\Exception $e) {
            return response($e, status: 400);
        }
    }
}