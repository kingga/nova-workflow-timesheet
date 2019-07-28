<?php

/**
 * The week resource.
 *
 * @author Isaac Skelton <contact@isaacskelton.com>
 * @since 1.0.0
 * @package Kingga\NovaWorkflowTimesheet\Resources
 */

namespace Kingga\NovaWorkflowTimesheet\Resources;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * The week resource.
 */
class Week extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Kingga\LaravelWorkflowTimesheet\WorkflowWeek';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'start',
        'end',
    ];

    /**
     * The group which this resource belongs to in the navigation.
     *
     * @var string
     */
    public static $group = 'Timesheet';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @return string
     */
    public function title()
    {
        $d = clone $this->start;
        $d->modify('friday this week');

        return sprintf(
            'Week %d of %s',
            ceil(((int) $d->format('d')) / 7),
            $d->format('F, Y')
        );
    }

    /**
     * The subtitle for this resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return sprintf('from %s to %s', $this->start->format('Y-m-d'), $this->end->format('Y-m-d'));
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('user_id', Auth::id());
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
            Date::make('Start'),

            Date::make('End'),

            HasMany::make('Entries'),
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
        return [];
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
        return [];
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
