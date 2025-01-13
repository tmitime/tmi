<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $team = $user->currentTeam()->first();

        return view('dashboard', [
            'projects' => Project::with('members')->ofTeam($team)->get(),
            'shared' => Project::with('members')->sharedTo($user, $team)->get(),
            'team' => $team,
        ]);
    }
}
