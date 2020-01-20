<?php

namespace Tests\SimpleCli\Options;

use Tests\SimpleCli\DemoApp\DemoCli;
use Tests\SimpleCli\TestCase;

/**
 * @coversDefaultClass \SimpleCli\Options\Quiet
 */
class QuietTest extends TestCase
{
    public function testIsQuiet()
    {
        self::assertOutput(
            '',
            function () {
                $command = new DemoCli();

                $command('file', 'create', '--quiet');
            }
        );

        self::assertOutput(
            '[ESCAPE][0;36m0 programs created.
[ESCAPE][0m',
            function () {
                $command = new DemoCli();

                $command('file', 'create');
            }
        );
    }
}
