<?php

namespace Tests\SimpleCli;

use SimpleCli\SimpleCliCommand;

/**
 * @coversDefaultClass \SimpleCli\SimpleCliCommand
 */
class SimpleCliCommandTest extends TestCase
{
    /**
     * @covers ::getPackageName
     */
    public function testGetPackageName()
    {
        self::assertSame('simple-cli/simple-cli', (new SimpleCliCommand())->getPackageName());
    }

    /**
     * @covers ::getCommands
     */
    public function testGetCommands()
    {
        self::assertSame(['create' => SimpleCliCommand\Create::class], (new SimpleCliCommand())->getCommands());
    }
}
