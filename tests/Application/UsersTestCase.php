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
use RDS\Hydrogen\Query;
use RDS\Hydrogen\Tests\Application\Mock\Entity\Message;
use RDS\Hydrogen\Tests\Application\Mock\Entity\User;
use RDS\Hydrogen\Tests\Application\Mock\Repository\UsersRepository;

/**
 * Class UsersTestCase
 */
class UsersTestCase extends QueryTestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testSimpleRelations(): void
    {
        $queries = $this->log(function () {
            /** @var User[] $users */
            $users = $this->getRepository()->query
                ->leftJoin('messages')
                ->get();

            foreach ($users as $user) {
                $this->assertGreaterThan(1, \count($user->messages));
            }
        });

        $this->assertCount(1, $queries);
    }

    /**
     * @return UsersRepository
     */
    protected function getRepository(): EntityRepository
    {
        $meta = $this->em->getClassMetadata(User::class);

        return new UsersRepository($this->em, $meta);
    }

    /**
     * @param Generator $faker
     * @return \Generator|User[]
     * @throws \Exception
     */
    protected function getMocks(Generator $faker): \Generator
    {
        for ($i = \random_int(6, 10); $i--;) {
            $user       = new User();
            $user->name = $faker->name;

            for ($j = \random_int(6, 10); $j--;) {
                $message          = new Message();
                $message->content = $faker->text(200);
                $message->author  = $user;

                $this->em->persist($message);
            }

            yield $user;
        }
    }
}
