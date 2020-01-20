<?php

namespace Tests\SimpleCli\Traits;

use Tests\SimpleCli\DemoApp\DemoCli;

/**
 * @coversDefaultClass \SimpleCli\Traits\Output
 */
class OutputTest extends TraitsTestCase
{
    /**
     * @covers ::write
     */
    public function testWrite()
    {
        $command = new DemoCli();

        self::assertOutput(
            'Hello world',
            function () use ($command) {
                $command->write('Hello world');
            }
        );

        self::assertOutput(
            '',
            function () use ($command) {
                $command->mute();
                $command->write('Hello world');
            }
        );

        self::assertOutput(
            '[ESCAPE][0;31mHello world[ESCAPE][0m',
            function () use ($command) {
                $command->unmute();
                $command->write('Hello world', 'red');
            }
        );
    }

    /**
     * @covers ::writeLine
     */
    public function testWriteLine()
    {
        $command = new DemoCli();

        self::assertOutput(
            "Hello world\n",
            function () use ($command) {
                $command->writeLine('Hello world');
            }
        );

        self::assertOutput(
            '',
            function () use ($command) {
                $command->mute();
                $command->writeLine('Hello world');
            }
        );

        self::assertOutput(
            "[ESCAPE][0;31mHello world\n[ESCAPE][0m",
            function () use ($command) {
                $command->unmute();
                $command->writeLine('Hello world', 'red');
            }
        );
    }

    /**
     * @covers ::colorize
     * @covers ::getColorCode
     */
    public function testColorize()
    {
        $command = new DemoCli();

        self::assertSame(
            'Hello world',
            $command->colorize('Hello world')
        );
        self::assertSame(
            'Hello world',
            $command->colorize('Hello world', null, null)
        );
        self::assertSame(
            '[ESCAPE][41mHello world[ESCAPE][0m',
            $command->colorize('Hello world', null, 'red')
        );
        self::assertSame(
            '[ESCAPE][0;34mHello world[ESCAPE][0m',
            $command->colorize('Hello world', 'blue')
        );
        self::assertSame(
            '[ESCAPE][0;34m[ESCAPE][43mHello world[ESCAPE][0m',
            $command->colorize('Hello world', 'blue', 'yellow')
        );
    }

    /**
     * @covers ::enableColors
     * @covers ::disableColors
     */
    public function testColorSupport()
    {
        $command = new DemoCli();

        self::assertSame(
            '[ESCAPE][0;34m[ESCAPE][43mHello world[ESCAPE][0m',
            $command->colorize('Hello world', 'blue', 'yellow')
        );

        $command->disableColors();

        self::assertSame('Hello world', $command->colorize('Hello world', 'blue', 'yellow'));

        $command->enableColors();

        self::assertSame(
            '[ESCAPE][0;34m[ESCAPE][43mHello world[ESCAPE][0m',
            $command->colorize('Hello world', 'blue', 'yellow')
        );
    }

    /**
     * @covers ::setEscapeCharacter
     */
    public function testSetEscapeCharacter()
    {
        $command = new DemoCli();

        self::assertSame(
            '[ESCAPE][41mHello world[ESCAPE][0m',
            $command->colorize('Hello world', null, 'red')
        );

        $command->setEscapeCharacter('#');

        self::assertSame(
            '#[41mHello world#[0m',
            $command->colorize('Hello world', null, 'red')
        );
    }

    /**
     * @covers ::setColors
     */
    public function testSetColors()
    {
        $command = new DemoCli();

        self::assertSame(
            '[ESCAPE][0;31m[ESCAPE][41mHello world[ESCAPE][0m',
            $command->colorize('Hello world', 'red', 'red')
        );

        $command->setColors(
            [
                'red' => 'ab',
            ],
            [
                'red' => 'xy',
            ]
        );

        self::assertSame(
            '[ESCAPE][abm[ESCAPE][xymHello world[ESCAPE][0m',
            $command->colorize('Hello world', 'red', 'red')
        );
    }

    /**
     * @covers ::rewind
     */
    public function testRewind()
    {
        $command = new DemoCli();

        self::assertOutput(
            'Hello world[ESCAPE][11D[ESCAPE][3D',
            function () use ($command) {
                $command->write('Hello world');
                $command->rewind();
                $command->rewind(3);
            }
        );

        $command = new DemoCli();

        self::assertOutput(
            'Hello world',
            function () use ($command) {
                $command->write('Hello world');
                $command->mute();
                $command->rewind();
                $command->rewind(3);
            }
        );
    }

    /**
     * @covers ::rewrite
     */
    public function testRewrite()
    {
        $command = new DemoCli();

        self::assertOutput(
            'Hello world[ESCAPE][11DBye',
            function () use ($command) {
                $command->write('Hello world');
                $command->rewrite('Bye');
            }
        );
    }

    /**
     * @covers ::rewriteLine
     */
    public function testRewriteLine()
    {
        $command = new DemoCli();

        self::assertOutput(
            "Hello world\n\rBye",
            function () use ($command) {
                $command->writeLine('Hello world');
                $command->rewriteLine('Bye');
            }
        );
    }

    /**
     * @covers ::isMuted
     * @covers ::setMuted
     * @covers ::mute
     * @covers ::unmute
     */
    public function testSetMute()
    {
        $command = new DemoCli();

        self::assertFalse($command->isMuted());

        $command->setMuted(true);

        self::assertTrue($command->isMuted());

        $command->setMuted(false);

        self::assertFalse($command->isMuted());

        $command->mute();

        self::assertTrue($command->isMuted());

        $command->unmute();

        self::assertFalse($command->isMuted());
    }
}
