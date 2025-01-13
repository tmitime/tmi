<?php

namespace App\Http\Controllers;

use App\Enum\ReportingPeriod;
use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $validated = $this->validate($request, [
            'period' => [new Enum(ReportingPeriod::class)],
            'from' => ['required_if:period,custom', 'date', 'before:to'],
            'to' => ['required_if:period,custom', 'date', 'after:from'],
        ]);

        $requestedPeriod = ReportingPeriod::tryFrom(e($validated['period'] ?? ReportingPeriod::CURRENT_MONTH->value)) ?? ReportingPeriod::CURRENT_MONTH;

        [$start_date, $end_date] = $requestedPeriod === ReportingPeriod::OVERALL ? [$project->start_at, $project->end_at ?? today()->endOfDay()] : $this->getPeriod($requestedPeriod, $validated['from'] ?? null, $validated['to'] ?? null);

        $rawDailySummary = $project
            ->tasks()
            ->period($start_date, $end_date)
            ->selectRaw('date_format(created_at, "%Y-%m-%d") as day, SUM(duration) as time, GROUP_CONCAT(description SEPARATOR " + ") as activities')
            ->groupBy(['day'])
            ->orderBy('day', 'asc')
            ->get()->keyBy('day');

        $periodTotalDuration = $project->tasks()->period($start_date, $end_date)->sum('duration');

        $working_days = $periodTotalDuration > 0 ? round($periodTotalDuration / Carbon::MINUTES_PER_HOUR / config('timetracking.working_day'), 2) : 0;

        $remaining_working_days = $project->working_days ? round($project->working_days - $working_days, 2) : null;

        $period = CarbonPeriod::since($start_date)->days(1)->until($end_date)->filter('isWeekday');

        $dailySummary = collect($period)->map(function ($item) use ($rawDailySummary) {

            $value = $rawDailySummary->get($item->toDateString());

            /** @var \Carbon\Carbon $item */
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
            'report_name' => $start_date->isSameMonth($end_date) ? __(':Month Report', ['month' => $start_date->localeMonth]) : __(':Start to :end Report', ['start' => $start_date->localeMonth, 'end' => $end_date->localeMonth]),
            'report_start_at' => $start_date,
            'report_end_at' => $end_date,
            'dailySummary' => $dailySummary,
        ]);
    }

    protected function getPeriod($period, $from = null, $to = null)
    {
        if ($period === ReportingPeriod::CUSTOM) {
            $start_date = new Carbon($from);
            $end_date = new Carbon($to);

            return [$start_date, $end_date];
        }

        if ($period === ReportingPeriod::PREVIOUS_MONTH) {
            $dateWithinLastMonth = today()->subMonthsNoOverflow(1)->toImmutable();

            $start_date = new Carbon($dateWithinLastMonth->startOfMonth());
            $end_date = new Carbon($dateWithinLastMonth->endOfMonth());

            return [$start_date, $end_date];
        }

        $today = today()->toImmutable();

        $start_date = new Carbon($today->startOfMonth());
        $end_date = new Carbon($today->endOfMonth());

        return [$start_date, $end_date];
    }
}
