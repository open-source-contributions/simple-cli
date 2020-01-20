<?php

namespace Tests\SimpleCli\Traits;

use Tests\SimpleCli\DemoApp\DemoCli;

/**
 * @coversDefaultClass \SimpleCli\Traits\Arguments
 */
class ArgumentsTest extends TraitsTestCase
{
    /**
     * @covers ::getArguments
     */
    public function testGetArguments()
    {
        $command = new DemoCli();
        $command->mute();

        $command('file', 'foobar');

        self::assertSame([], $command->getArguments());

        $command('file', 'foobar', 'My sentence');

        self::assertSame(
            [
                'sentence' => 'My sentence',
            ],
            $command->getArguments()
        );
    }

    /**
     * @covers ::getExpectedArguments
     */
    public function testGetExpectedArguments()
    {
        $command = new DemoCli();
        $command->mute();

        $command('file', 'version');

        self::assertSame([], $command->getExpectedArguments());

        $command('file', 'foobar');

        self::assertSame(
            [
                [
                    'property'    => 'sentence',
                    'description' => 'Sentence to display.',
                    'values'      => null,
                    'type'        => 'string',
                ],
            ],
            $command->getExpectedArguments()
        );
    }

    /**
     * @covers ::getRestArguments
     */
    public function testGetRestArguments()
    {
        $command = new DemoCli();
        $command->mute();

        $command('file', 'foobar', 'My sentence', 'A', 'B');

        self::assertSame([], $command->getRestArguments());

        $command('file', 'rest', 'My sentence', 'A', 'B');

        self::assertSame(['A', 'B'], $command->getRestArguments());
    }

    /**
     * @covers ::getExpectedRestArgument
     */
    public function testGetExpectedRestArgument()
    {
        $command = new DemoCli();
        $command->mute();

        $command('file', 'version');

        self::assertNull($command->getExpectedRestArgument());

        $command('file', 'rest');

        self::assertSame(
            [
                'property'    => 'suffixes',
                'description' => 'Suffixes after the sentence.',
                'values'      => null,
                'type'        => 'string',
            ],
            $command->getExpectedRestArgument()
        );
    }

    /**
     * @covers ::parseArgument
     */
    public function testParseArgument()
    {
        $command = new DemoCli();
        $command->disableColors();

        self::assertOutput(
            'Expect only 0 arguments',
            function () use ($command) {
                $command('file', 'version', 'too-argument');
            }
        );

        self::assertOutput(
            "Hello world!\n",
            function () use ($command) {
                $command('file', 'rest', 'Hello', ' world', '!');
            }
        );
    }
}
