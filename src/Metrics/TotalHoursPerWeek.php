<?php

namespace Kingga\NovaWorkflowTimesheet\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Metrics\TrendResult;
use Kingga\LaravelWorkflowTimesheet\WorkflowWeek;
use Kingga\LaravelWorkflowTimesheet\WorkflowEntry;

class TotalHoursPerWeek extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $entries = collect([]);
        $results = [];
        $range = (int) $request->range;

        $week_ids = WorkflowWeek::where('start', '>=', now()->modify("-$range days"))
            ->where('user_id', Auth::id())
            ->get(['id', 'start']);

        if ($week_ids) {
            $entries = WorkflowEntry::whereIn('week_id', $week_ids->pluck('id'))
                ->join('workflow_weeks', 'workflow_weeks.id', '=', 'week_id')
                ->where('user_id', Auth::id())
                ->groupBy('week_id')
                ->orderBy('start', 'asc')
                ->get(['week_id', DB::raw('SUM(time) AS time', 'start')]);
        }

        $entries->each(function ($entry) use ($week_ids, &$results) {
            $date = $week_ids->where('id', $entry->week_id)->first()->start;
            $date = sprintf(
                '%s, Week %d',
                $date->format('Y F'),
                ceil(((int) $date->format('d')) / 7)
            );

            $results[$date] = $entry->time;
        });

        return (new TrendResult)->trend($results);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            28 => '4 Weeks',
            56 => '8 Weeks',
            84 => '12 Weeks',
            365 => '1 Year',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // Every 60 minutes, this should only really be updated once a week anyway.
        return now()->addMinutes(60);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'total-hours-per-week';
    }
}
