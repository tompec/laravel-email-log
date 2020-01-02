<?php

namespace Tompec\LaravelEmailLog;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tompec\LaravelEmailLog\Skeleton\SkeletonClass
 */
class LaravelEmailLogFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-email-log';
    }
}
