<?php

namespace Kingga\NovaWorkflowTimesheet\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Metrics\ValueResult;
use Kingga\LaravelWorkflowTimesheet\WorkflowWeek;
use Kingga\LaravelWorkflowTimesheet\WorkflowEntry;

class TotalHoursForNewZealandTaxYear extends Value
{
    /**
     * The name to display on this metric.
     *
     * @var string
     */
    public $name = 'Total Hours For Tax Year';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $results = [];
        $range = $request->range - 1;
        $end = now()->modify("-$range Years")->format('Y');
        $start = now()->modify('-' . ($range + 1) . ' Years')->format('Y');

        // New Zealand tax year ends on the 31st March.
        $time = WorkflowEntry::where('date', '>', "$start-03-31")
            ->join('workflow_weeks', 'workflow_weeks.id', '=', 'week_id')
            ->where('date', '<=', "$end-03-31")
            ->where('user_id', Auth::id())
            ->sum('time');

        return new ValueResult($time);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        // Get how many years the user can go back.
        $first = WorkflowWeek::where('user_id', Auth::id())
            ->orderBy('start')
            ->first();

        $date = now();
        $diff = $date->diffInYears($first->start);
        $years = [];

        for ($i = 0; $i <= $diff; $i++) {
            $label = '';

            if ($i === 0) {
                $label = 'This Year (Incomplete)';
            } elseif ($i === 1) {
                $label = 'Last Tax Year';
            } elseif ($i > 1) {
                $label = "$i Years Ago";
            }

            $years[$i] = $label;
        }

        return $years;
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // Every 60 minutes, this should only really be updated once a week anyway.
        // return now()->addMinutes(60);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'total-hours-for-new-zealand-tax-year';
    }
}
