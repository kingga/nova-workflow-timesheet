<?php

/**
 * The service provider for this package.
 *
 * @author Isaac Skelton <contact@isaacskelton.com>
 * @since 1.0.0
 * @package Kingga\NovaWorkflowTimesheet
 */

namespace Kingga\NovaWorkflowTimesheet;

use Laravel\Nova\Nova;
use Illuminate\Support\ServiceProvider;
use Kingga\NovaWorkflowTimesheet\Resources\Job;
use Kingga\NovaWorkflowTimesheet\Resources\Task;
use Kingga\NovaWorkflowTimesheet\Resources\Week;
use Kingga\NovaWorkflowTimesheet\Resources\Entry;

/**
 * The service provider.
 */
class NovaWorkflowTimesheet extends ServiceProvider
{
    /**
     * Called when loading the service provider.
     */
    public function boot()
    {
        Nova::resources([
            Entry::class,
            Job::class,
            Task::class,
            Week::class,
        ]);
    }
}
