<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Application\Mock\Entity;

use Doctrine\ORM\Mapping as ORM;
use RDS\Hydrogen\Tests\Application\Mock\Entity\Common\Timestamps;

/**
 * Class BaseEntity
 * @ORM\HasLifecycleCallbacks()
 */
abstract class BaseEntity
{
    use Timestamps;

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue()
     * @var int
     */
    public $id;
}
