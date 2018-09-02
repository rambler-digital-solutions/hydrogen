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
use RDS\Hydrogen\Tests\Application\Mock\Repository\MessagesRepository;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 * @ORM\Table(name="messages")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue()
     * @var int
     */
    public $id;

    /**
     * @ORM\Column(name="content", type="text")
     * @var string
     */
    public $content = '';

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $author;
}
