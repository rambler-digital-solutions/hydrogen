<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Application\Mock\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RDS\Hydrogen\Tests\Application\Mock\Repository\MessagesRepository;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 * @ORM\Table(name="messages")
 */
class Message extends BaseEntity
{
    /**
     * @ORM\Column(name="content", type="text")
     * @var string
     */
    public $content = '';

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $author;

    /**
     * @var ArrayCollection|User[]
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="likedMessages", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinTable(name="likes",
     *  joinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id") },
     *  inverseJoinColumns={ @ORM\JoinColumn(name="message_id", referencedColumnName="id") },
     * )
     */
    public $likedBy;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="deleted_at", type="date")
     */
    public $deletedAt;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->likedBy = new ArrayCollection();
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $this->deletedAt = new \DateTime();
    }

    /**
     * @return void
     */
    public function restore(): void
    {
        $this->deletedAt = null;
    }
}
