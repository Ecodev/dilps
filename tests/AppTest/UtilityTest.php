<?php

declare(strict_types=1);

namespace ApplicationTest;

use App\Model\User;
use App\Utility;

class UtilityTest extends \PHPUnit\Framework\TestCase
{
    public function testNow(): void
    {
        $this->assertStringStartsWith('201', Utility::getNow()->format('c'));
    }

    public function testGetShortClassName(): void
    {
        $this->assertSame('User', Utility::getShortClassName(new User()));
    }
}
