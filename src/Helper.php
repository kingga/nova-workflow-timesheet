<?php

/**
 * A simple class which helps do things. For example, formatting a decimal time
 * into a human readable form or formatting it as the full string used in the resources.
 *
 * @author Isaac Skelton <contact@isaacskelton.com>
 * @since 1.0.0
 * @package Kingga\NovaWorkflowTimesheet
 */

namespace Kingga\NovaWorkflowTimesheet;

/**
 * The static helper class.
 */
class Helper
{
    /**
     * Convert a decimal time e.g. 1.5 into the human readable format, e.g. 01:30.
     *
     * @param float $float The decimal time to convert.
     *
     * @return string The human readable format.
     */
    public static function timeFloatToHumanReadable(float $float): string
    {
        // Convert the float into the format: '00:00'.
        $hours = str_pad((string) floor($float), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad(
            // Subtract the hours so we are just left with he minutes and then change it back
            // to the readable format.
            (string) floor(($float - floor($float)) * 60),
            2,
            '0',
            STR_PAD_LEFT
        );

        return sprintf('%s:%s', $hours, $minutes);
    }

    /**
     * Convert the human readable time string into the decimal format. For example,
     * from 01:30 to 1.5.
     *
     * @param string $readable The readable format.
     *
     * @return float The converted decimal time.
     */
    public static function timeHumanReadableToFloat(string $readable): float
    {
        [$hours, $minutes] = explode(':', $readable);
        $minutes /= 60;

        return ((int) $hours) + $minutes;
    }

    /**
     * Format time to be displayed inside of the resources.
     *
     * @param float $float The time to format.
     */
    public static function formatTime(float $float)
    {
        return sprintf('%s (%.2f)', self::timeFloatToHumanReadable($float), $float);
    }
}
