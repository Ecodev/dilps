<?php

declare(strict_types=1);

namespace ApplicationTest\ORM\Query\Filter;

use Application\Model\Artist;
use Application\Model\Card;
use Application\Model\Change;
use Application\Model\Collection;
use Application\Model\Country;
use Application\Model\Dating;
use Application\Model\Institution;
use Application\Model\Tag;
use Application\Model\User;

use Application\ORM\Query\Filter\AclFilter;
use PHPUnit\Framework\TestCase;

class AclFilterTest extends TestCase
{
    public function providerFilter(): array
    {
        return [
            'country is totally public public class, access everything' => [
                null,
                Country::class,
                '',
            ],
            'artist is totally public public class, access everything' => [
                null,
                Artist::class,
                '',
            ],
            'institution is totally public public class, access everything' => [
                null,
                Institution::class,
                '',
            ],
            'tag is totally public public class, access everything' => [
                null,
                Tag::class,
                '',
            ],
            'dating is totally public public class, access everything' => [
                null,
                Dating::class,
                '',
            ],
            'users are invisible to anonymous' => [
                null,
                User::class,
                'test.id IN (-1)',
            ],
            'users are accessible to any other users' => [
                'student',
                User::class,
                'test.id IN (SELECT',
            ],
            'only public cards are accessible to anonymous' => [
                null,
                Card::class,
                'test.id IN (SELECT card.id FROM card WHERE card.visibility IN (\'public\'))',
            ],
            'student can access cards that are his own or are public or member' => [
                'student',
                Card::class,
                'test.id IN (SELECT card.id FROM card WHERE (card.visibility IN (\'public\', \'member\')) OR (card.creator_id = \'1003\'))',
            ],
            'only public collections are accessible to anonymous' => [
                null,
                Collection::class,
                'test.id IN (SELECT collection.id FROM collection WHERE collection.visibility IN (\'public\'))',
            ],
            'student can access collections that are his own or are public or member' => [
                'student',
                Collection::class,
                'test.id IN (SELECT collection.id FROM collection WHERE (collection.visibility IN (\'public\', \'member\')) OR (collection.creator_id = \'1003\'))',
            ],
            'changes are invisible to anonymous' => [
                null,
                Change::class,
                'test.id IN (-1)',
            ],
            'student can access changes that are his own' => [
                'student',
                Change::class,
                'test.id IN (SELECT `change`.id FROM `change` WHERE `change`.creator_id = \'1003\')',
            ],
        ];
    }

    /**
     * @dataProvider providerFilter
     *
     * @param null|string $login
     * @param string $class
     * @param string $expected
     */
    public function testFilter(?string $login, string $class, string $expected): void
    {
        $user = _em()->getRepository(User::class)->getOneByLogin($login);
        $filter = new AclFilter(_em());
        $filter->setUser($user);
        $targetEntity = _em()->getMetadataFactory()->getMetadataFor($class);
        $actual = $filter->addFilterConstraint($targetEntity, 'test');

        if ($expected === '') {
            self::assertSame($expected, $actual);
        } else {
            self::assertStringStartsWith($expected, $actual);
        }
    }

    public function testDeactivable(): void
    {
        $user = new User();
        $filter = new AclFilter(_em());
        $filter->setUser($user);
        $targetEntity = _em()->getMetadataFactory()->getMetadataFor(Card::class);

        $this->assertNotSame('', $filter->addFilterConstraint($targetEntity, 'test'));

        $filter->setEnabled(false);
        $this->assertSame('', $filter->addFilterConstraint($targetEntity, 'test'));

        $filter->setEnabled(true);
        $this->assertNotSame('', $filter->addFilterConstraint($targetEntity, 'test'));
    }
}
