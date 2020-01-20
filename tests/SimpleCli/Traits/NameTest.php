<?php

namespace Tests\SimpleCli\Traits;

use Tests\SimpleCli\DemoApp\DemoCli;
use Tests\SimpleCli\DemoApp\DummyCli;

/**
 * @coversDefaultClass \SimpleCli\Traits\Name
 */
class NameTest extends TraitsTestCase
{
    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $command = new DemoCli();

        self::assertNull($command->getName());

        $command = new DummyCli();

        self::assertSame('stupid', $command->getName());
    }
}
