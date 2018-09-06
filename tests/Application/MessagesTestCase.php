<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Application;

use Doctrine\ORM\EntityRepository;
use RDS\Hydrogen\Tests\Application\Mock\Entity\Message;
use RDS\Hydrogen\Tests\Application\Mock\Entity\User;
use RDS\Hydrogen\Tests\Application\Mock\Repository\MessagesRepository;

/**
 * Class MessagesTestCase
 */
class MessagesTestCase extends QueryTestCase
{
    /**
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Message::class;
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testSimpleRelations(): void
    {
        $queries = $this->log(function () {
            /** @var Message[] $messages */
            $messages = $this->getRepository()->query
                ->join('author', 'likedBy')
                ->get();

            foreach ($messages as $message) {
                $this->assertInternalType('string', $message->author->name);

                foreach ($message->likedBy as $user) {
                    $this->assertInstanceOf(User::class, $user);
                }
            }

        });

        $this->assertCount(2, $queries, \print_r($queries, true));
    }

    /**
     * @return MessagesRepository
     */
    protected function getRepository(): EntityRepository
    {
        $meta = $this->em->getClassMetadata(Message::class);

        return new MessagesRepository($this->em, $meta);
    }
}
