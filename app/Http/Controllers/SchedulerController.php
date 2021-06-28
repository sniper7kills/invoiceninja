<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class SchedulerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->company()->account->latest_version == '0.0.0') {
            return response()->json(['message' => ctrans('texts.scheduler_has_never_run')], 400);
        } else {
            return response()->json(['message' => ctrans('texts.scheduler_has_run')], 200);
        }
    }
}
