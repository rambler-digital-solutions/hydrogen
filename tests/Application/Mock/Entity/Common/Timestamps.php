<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Application\Mock\Entity\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait Timestamps
 * @ORM\HasLifecycleCallbacks()
 */
trait Timestamps
{
    /**
     * @var \DateTime|null
     * @ORM\Column(name="created_at", type="date")
     */
    public $createdAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="updated_at", type="date")
     */
    public $updatedAt;

    /**
     * @ORM\PrePersist()
     * @return void
     */
    public function bootTimestamps(): void
    {
        $this->createdAt = new \DateTime();
        $this->touch();
    }

    /**
     * @ORM\PreUpdate()
     * @return void
     */
    public function touch(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
