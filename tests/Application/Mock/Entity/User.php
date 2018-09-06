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
use RDS\Hydrogen\Tests\Application\Mock\Repository\UsersRepository;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @ORM\Table(name="users")
 */
class User extends BaseEntity
{
    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @var string
     */
    public $name;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="author", cascade={"persist", "remove"})
     * @var ArrayCollection|Message[]
     */
    public $messages;

    /**
     * @var ArrayCollection|Message[]
     * @ORM\ManyToMany(targetEntity=Message::class, inversedBy="likedBy", cascade={"persist"})
     * @ORM\JoinTable(name="likes",
     *  joinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id") },
     *  inverseJoinColumns={ @ORM\JoinColumn(name="message_id", referencedColumnName="id") },
     * )
     */
    public $likedMessages;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->likedMessages = new ArrayCollection();
    }
}
