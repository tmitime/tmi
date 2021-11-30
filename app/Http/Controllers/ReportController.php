<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DatePeriod;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $today = today()->toImmutable();

        $start_date = new Carbon($today->startOfMonth());
        $end_date = new Carbon($today->endOfMonth());

        $rawDailySummary = $project->tasks()->period($start_date, $end_date)
            ->selectRaw('date_format(created_at, "%Y-%m-%d") as day, SUM(duration) as time, GROUP_CONCAT(description SEPARATOR " + ") as activities')
            ->groupBy(['day'])
            ->orderBy('day', 'asc')
            ->get()->keyBy('day');

        $periodTotalDuration = $project->tasks()->period($start_date, $end_date)->sum('duration');

        $working_days = $periodTotalDuration > 0 ? round( $periodTotalDuration / Carbon::MINUTES_PER_HOUR / config('timetracking.working_day'), 2) : 0;
        
        $remaining_working_days = $project->working_days ? round( $project->working_days - $working_days, 2) : null;

        $period = CarbonPeriod::since($start_date)->days(1)->until($end_date)->filter('isWeekday');

        $dailySummary = collect($period)->map(function($item) use ($rawDailySummary) {

            $value = $rawDailySummary->get($item->toDateString());

            /** @var \Carbon\Carbon $item  */
            return [
                'day' => $item,
                'activities' => optional($value)->activities ?? '',
                'duration' => optional($value)->time ?? 0,
            ];
        });

        return view('projects.report', [
            'project' => $project,
            'working_days' => $working_days,
            'remaining_working_days' => $remaining_working_days,
            'report_name' => __(':Month Report', ['month' => $start_date->localeMonth]),
            'report_start_at' => $start_date,
            'report_end_at' => $end_date,
            'dailySummary' => $dailySummary,
        ]);
    }
}
