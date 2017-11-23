<?php

declare(strict_types=1);

namespace ApplicationTest;

use App\Utility;

class UtilityTest extends \PHPUnit\Framework\TestCase
{
    public function testNow(): void
    {
        $this->assertEquals('2015-01-02T03:04:00+00:00', Utility::getNow()->format('c'), 'tests are always executed at a fixed time');
    }

    public function testGetShortClassName(): void
    {
        $this->assertSame('User', Utility::getShortClassName(new \App\Model\User()));
    }
}
