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
                'jobs' => $jobs,
                'message' => $jobs->isEmpty() ? 'No jobs found.' : null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'error' => $e->getMessage()], 400);
        }
    }
}
