<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobPosted;
use App\Models\Job;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $jobs = Job::latest()->with(['employer', 'tags'])->get()->groupBy('featured');

            // return view('jobs.index',[
            //     'featuredJobs'=>$jobs[1],'jobs'=>$jobs[0],'tags'=>Tag::all()]);

            return response()->json([
                'status' => 'success',
                'jobs' => $jobs
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     // return view('jobs.create');
    //     return ('/jobs');
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $attributes = $request->validate([
                'title' => ['required'],
                'salary' => ['required'],
                'location' => ['required'],
                'schedule' => ['required', Rule::in(['Part Time', 'Full Time'])],
                'url' => ['required', 'active_url'],
                'featured' => ['required', Rule::in([true, false])],
                'tags' => ['nullable']
            ]);

            // $attributes['featured'] = $request->has('featured');

            $job = $request->user()->employer->jobs()->create(Arr::except($attributes, 'tags'));


            if ($attributes['tags'] ?? false) {
                foreach (explode(',', $attributes['tags']) as $tag) {
                    $job->tag($tag);
                }
            }
            //         return redirect('/');
            Mail::to($job->employer->user)->queue(new JobPosted($job));

            return response()->json([
                'status' => 'success',
                'jobs' => $job
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function show(Job $job)
    {
        try {
            $job->load('tags');

            return response()->json([
                'status' => 'success',
                'job' => $job
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Job $job)
    {
        try {
            $attributes = request()->validate([
                'title' => ['required'],
                'salary' => ['required'],
                'location' => ['required'],
                'schedule' => ['required', Rule::in(['Part Time', 'Full Time'])],
                'url' => ['required', 'active_url'],
                'featured' => ['required', Rule::in([true, false])],

                'tags' => ['nullable']
            ]);

            // $attributes['featured'] = request()->has('featured');

            // $job = request()->user()->employer->jobs()->update(Arr::except($attributes, 'tags'));
            $job->update(Arr::except($attributes, 'tags'));

            if (!empty($attributes['tags'])) {
                // Process tags
                $tags = array_map('trim', explode(',', $attributes['tags']));
                // foreach (explode(',', $attributes['tags']) as $tag) {

                //     $job->tag($tag);
                // }

                // Sync tags to avoid duplicates
                $tagIds = [];
                foreach ($tags as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }

                // Use sync to attach tags without duplicates
                $job->tags()->sync($tagIds);
            } else {
                // If no tags are provided, clear existing tags
                $job->tags()->detach();
            }
            //         return redirect('/');
            return response()->json([
                'status' => 'success',
                'job' => $job
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(Job $job)
    {
        try {
            // Auth::user()->employer->jobs()->delete();
            request()->user()->employer->jobs()->delete();
            // $job->delete();
            return ['message' => 'Job Deleted'];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
