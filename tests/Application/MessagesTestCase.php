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
use Faker\Generator;
use RDS\Hydrogen\Tests\Application\Mock\Entity\Message;
use RDS\Hydrogen\Tests\Application\Mock\Entity\User;
use RDS\Hydrogen\Tests\Application\Mock\Repository\MessagesRepository;

/**
 * Class MessagesTestCase
 */
class MessagesTestCase extends QueryTestCase
{
    /**
     * @param Generator $faker
     * @return \Generator|User[]
     * @throws \Exception
     */
    protected function getMocks(Generator $faker): \Generator
    {
        for ($i = \random_int(6, 20); $i--;) {
            $user = new User();
            $user->name = $faker->name;

            for ($j = \random_int(1, 15); $j--;) {
                $message = new Message();
                $message->content = $faker->text(200);
                $message->author = $user;

                yield $message;
            }
        }
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
