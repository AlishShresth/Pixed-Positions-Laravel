<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployerController extends Controller
{
    public function getEmployer()
    {
        try {
            $employer = request()->user()->employer;
            if (!$employer) {
                return response()->json([
                    'status' =>
                    'fail',
                    'error' => 'No employer found.',
                ], 400);
            }
            return response()->json([
                'status' => 'success',
                'employer' => $employer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
