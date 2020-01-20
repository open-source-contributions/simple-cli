<?php

namespace Tests\SimpleCli\Traits;

use Tests\SimpleCli\DemoApp\DemoCli;

/**
 * @coversDefaultClass \SimpleCli\Traits\File
 */
class FileTest extends TraitsTestCase
{
    /**
     * @covers ::getFile
     */
    public function testGetFile()
    {
        $command = new DemoCli();
        $command->mute();

        $command('foobar');

        self::assertSame('foobar', $command->getFile());
    }
}
