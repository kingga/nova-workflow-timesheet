<?php

/**
 * The entry resource.
 *
 * @author Isaac Skelton <contact@isaacskelton.com>
 * @since 1.0.0
 * @package Kingga\NovaWorkflowTimesheet\Resources
 */

namespace Kingga\NovaWorkflowTimesheet\Resources;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Kingga\NovaWorkflowTimesheet\Helper;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;
use Kingga\NovaWorkflowTimesheet\Lenses\HoursPerDay;
use Kingga\NovaWorkflowTimesheet\Lenses\HoursPerTask;
use Kingga\NovaWorkflowTimesheet\Lenses\HoursPerWeek;
use Kingga\NovaWorkflowTimesheet\Metrics\TotalHoursPerWeek;
use Kingga\NovaWorkflowTimesheet\Metrics\TotalHoursForNewZealandTaxYear;

/**
 * The entry resource.
 */
class Entry extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Kingga\LaravelWorkflowTimesheet\WorkflowEntry';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'task' => ['name'],
    ];

    /**
     * The group which this resource belongs to in the navigation.
     *
     * @var string
     */
    public static $group = 'Timesheet';

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->join('workflow_weeks', 'workflow_entries.week_id', '=', 'workflow_weeks.id')
            ->where('workflow_weeks.user_id', Auth::id())
            ->select(['workflow_entries.*']);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            BelongsTo::make('Week'),

            BelongsTo::make('Task')->sortable(),

            Date::make('Date')->sortable(),

            Text::make('Time')
                ->sortable()
                ->displayUsing(function ($float_time) {
                    return Helper::formatTime($float_time);
                })
                ->exceptOnForms(),

            // Closest thing to decimal/floating point field.
            Currency::make('Time')->onlyOnForms(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new TotalHoursPerWeek,
            new TotalHoursForNewZealandTaxYear,
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [
            new HoursPerWeek,
            new HoursPerDay,
            new HoursPerTask,
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
