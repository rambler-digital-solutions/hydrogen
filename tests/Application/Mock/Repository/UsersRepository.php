<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Application\Mock\Repository;

use Doctrine\ORM\EntityRepository;
use RDS\Hydrogen\Hydrogen;

/**
 * Class UsersRepository
 */
class UsersRepository extends EntityRepository
{
    use Hydrogen;
}
