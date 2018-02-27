<?php

declare(strict_types=1);

namespace ApplicationTest\Acl;

use Application\Acl\ModelResource;
use Application\Model\Card;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ModelResourceTest extends TestCase
{
    public function testConstructorVariants(): void
    {
        // Constructor with pre-loaded model
        $card = new Card();
        $resource = new ModelResource(Card::class, $card);
        self::assertSame($card, $resource->getInstance(), 'should be able to get back model');
        self::assertSame('Card#', $resource->getName(), 'should have unique name');
    }

    public function testUnknownClassMustThrow(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ModelResource('non-existing-class-name');
    }
}
