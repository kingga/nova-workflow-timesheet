<?php

namespace Kingga\NovaWorkflowTimesheet\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Lenses\Lens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Kingga\NovaWorkflowTimesheet\Helper;
use Laravel\Nova\Http\Requests\LensRequest;

class HoursPerDay extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->groupBy('date')
                ->groupBy('week_id')
                ->join('workflow_weeks', 'workflow_weeks.id', '=', 'week_id')
                ->where('user_id', Auth::id())
                ->orderBy('time', 'desc')
                ->select(self::columns())
        ));
    }

    /**
     * Get the columns that should be selected.
     *
     * @return array
     */
    protected static function columns()
    {
        return [
            'week_id',
            'date',
            DB::raw('SUM(time) AS time'),
        ];
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Date::make('Date'),

            Text::make('Time', 'time')
                ->sortable()
                ->displayUsing(function ($float_time) {
                    return Helper::formatTime($float_time);
                }),
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'hours-per-day';
    }
}
