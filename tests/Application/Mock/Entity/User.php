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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RDS\Hydrogen\Tests\Application\Mock\Repository\UsersRepository;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue()
     * @var int
     */
    public $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @var string
     */
    public $name;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="author", cascade={"persist"}, fetch="EAGER")
     * @var ArrayCollection|Message[]
     */
    public $messages;
}
