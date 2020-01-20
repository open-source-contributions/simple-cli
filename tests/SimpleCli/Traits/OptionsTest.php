<?php

namespace Tests\SimpleCli\Traits;

use InvalidArgumentException;
use Tests\SimpleCli\DemoApp\DemoCli;
use Tests\SimpleCli\DemoApp\DummyCli;

/**
 * @coversDefaultClass \SimpleCli\Traits\Options
 */
class OptionsTest extends TraitsTestCase
{
    /**
     * @covers ::getOptions
     */
    public function testGetOptions()
    {
        $command = new DummyCli();
        $command->mute();

        $command('file');

        self::assertSame([], $command->getOptions());

        $command = new DemoCli();
        $command->mute();

        $command('file');

        self::assertSame([], $command->getOptions());

        $command('file', 'foobar');

        self::assertSame([], $command->getOptions());
    }

    /**
     * @covers ::getExpectedOptions
     */
    public function testGetExpectedOptions()
    {
        $command = new DummyCli();
        $command->mute();

        $command('file');

        self::assertSame([], $command->getExpectedOptions());

        $command = new DemoCli();
        $command->mute();

        $command('file');

        self::assertSame([], $command->getExpectedOptions());

        $command = new DemoCli();
        $command->mute();

        $command('file', 'foobar');

        self::assertSame(
            [
                [
                    'property'    => 'prefix',
                    'names'       => [
                        'prefix',
                        'p',
                    ],
                    'description' => 'Append a prefix to $sentence.',
                    'values'      => 'hello, hi, bye',
                    'type'        => 'string',
                ],
                [
                    'property'    => 'verbose',
                    'names'       => [
                        'verbose',
                        'v',
                    ],
                    'description' => 'If this option is set, extra debug information will be displayed.',
                    'values'      => null,
                    'type'        => 'bool',
                ],
                [
                    'property'    => 'help',
                    'names'       => [
                        'help',
                        'h',
                    ],
                    'description' => 'Display documentation of the current command.',
                    'values'      => null,
                    'type'        => 'bool',
                ],
            ],
            $command->getExpectedOptions()
        );
    }

    /**
     * @covers ::getOptionDefinition
     */
    public function testGetOptionDefinition()
    {
        $command = new DemoCli();
        $command->mute();

        $command('file', 'foobar');

        self::assertSame(
            [
                'property'    => 'prefix',
                'names'       => [
                    'prefix',
                    'p',
                ],
                'description' => 'Append a prefix to $sentence.',
                'values'      => 'hello, hi, bye',
                'type'        => 'string',
            ],
            $command->getOptionDefinition('prefix')
        );
    }

    /**
     * @covers ::getOptionDefinition
     */
    public function testUnknownOptionName()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Unknown --xyz option');

        $command = new DemoCli();
        $command->mute();

        $command('file', 'foobar');

        $command->getOptionDefinition('xyz');
    }

    /**
     * @covers ::getOptionDefinition
     */
    public function testUnknownOptionAlias()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Unknown -x option');

        $command = new DemoCli();
        $command->mute();

        $command('file', 'foobar');

        $command->getOptionDefinition('x');
    }

    /**
     * @covers ::enableBooleanOption
     */
    public function testEnableBooleanOption()
    {
        $command = new DemoCli();
        $command->mute();

        $command('file', 'foobar');

        self::assertSame([], $command->getOptions());

        $command('file', 'foobar', '-h');

        self::assertSame(
            [
                'help' => true,
            ],
            $command->getOptions()
        );
    }

    /**
     * @covers ::enableBooleanOption
     */
    public function testEnableBooleanOptionOnNonBoolean()
    {
        self::assertOutput(
            '[ESCAPE][0;31m-p option is not a boolean, so you can\'t use it in a aliases group[ESCAPE][0m',
            function () {
                $command = new DemoCli();

                $command('file', 'foobar', '-p');
            }
        );
    }

    /**
     * @covers ::enableBooleanOption
     */
    public function testEnableBooleanOptionWithValue()
    {
        self::assertOutput(
            '[ESCAPE][0;31m-h option is boolean and should not have value[ESCAPE][0m',
            function () {
                $command = new DemoCli();

                $command('file', 'foobar', '-h=yoh');
            }
        );
    }

    /**
     * @covers ::setOption
     */
    public function testSetOption()
    {
        $command = new DemoCli();
        $command->mute();

        $command('file', 'foobar', '-p=hello');

        self::assertSame(
            [
                'prefix' => 'hello',
            ],
            $command->getOptions()
        );

        $command('file', 'foobar', '--help', '--prefix', 'hello');

        self::assertSame(
            [
                'help'   => true,
                'prefix' => 'hello',
            ],
            $command->getOptions()
        );
    }

    /**
     * @covers ::parseOption
     */
    public function testParseOption()
    {
        $command = new DemoCli();

        self::assertOutput(
            '[ESCAPE][0;31mUnable to parse -prefix=hello, maybe you would mean --prefix=hello[ESCAPE][0m',
            function () use ($command) {
                $command('file', 'foobar', '-prefix=hello');
            }
        );

        $command->mute();

        $command('file', 'foobar', '-vh');

        self::assertSame(
            [
                'verbose' => true,
                'help'    => true,
            ],
            $command->getOptions()
        );

        $command('file', 'foobar', '-hv');

        self::assertSame(
            [
                'help'    => true,
                'verbose' => true,
            ],
            $command->getOptions()
        );

        $command('file', 'foobar', '-p=hi');

        self::assertSame(
            [
                'prefix' => 'hi',
            ],
            $command->getOptions()
        );

        $command('file', 'foobar', '--prefix=bye');

        self::assertSame(
            [
                'prefix' => 'bye',
            ],
            $command->getOptions()
        );

        $command('file', 'foobar', '--prefix', 'bye');

        self::assertSame(
            [
                'prefix' => 'bye',
            ],
            $command->getOptions()
        );
    }
}
