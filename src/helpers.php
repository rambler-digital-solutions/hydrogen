<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace {

    /**
     * This pattern is used to specify the location of the delegate in
     * the function arguments in the high-order messaging.
     *
     * <code>
     *  $array = Collection::make(...)->map->intval(_, 10)->toArray();
     *
     *  // Is similar with:
     *
     *  $array = \array_map(function ($item): int {
     *       return \intval($item, 10);
     *                      ^^^^^ - pattern "_" will replaced to each delegated item value.
     *  }, ...);
     * </code>
     */
    if (! \defined('_')) {
        \define('_', \RDS\Hydrogen\HighOrderMessaging\HigherOrderCollectionProxy::PATTERN);
    }

    // ---------------------------------------------
    // Polyfills
    // ---------------------------------------------

    /**
     * @since 0.3.4
     */
    if (! \class_exists(\RDS\Hydrogen\Collection\Collection::class)) {
        \class_alias(\RDS\Hydrogen\Collection::class, \RDS\Hydrogen\Collection\Collection::class);
    }
}
