<?php

namespace Tests\SimpleCli\Traits;

use InvalidArgumentException;
use Tests\SimpleCli\DemoApp\DemoCli;

/**
 * @coversDefaultClass \SimpleCli\Traits\Parameters
 */
class ParametersTest extends TraitsTestCase
{
    /**
     * @covers ::getParameters
     */
    public function testGetParameters()
    {
        $command = new DemoCli();
        $command->mute();

        $command('hello');

        self::assertSame([], $command->getParameters());

        $command('hello', 'foobar', 'A', 'B', 'C');

        self::assertSame(['A', 'B', 'C'], $command->getParameters());
    }

    /**
     * @covers ::getParameterValue
     */
    public function testGetParameterValue()
    {
        $command = new DemoCli();

        self::assertSame(
            42,
            $command->getParameterValue(
                '42',
                [
                    'type'   => 'int',
                    'values' => '42',
                ]
            )
        );

        self::assertSame(
            42,
            $command->getParameterValue(
                '42.5',
                [
                    'type'   => 'int',
                    'values' => null,
                ]
            )
        );

        self::assertSame(
            42.5,
            $command->getParameterValue(
                '42.5',
                [
                    'type'   => 'float',
                    'values' => '42.5, 1',
                ]
            )
        );
    }

    /**
     * @covers ::getParameterValue
     */
    public function testGetParameterValueWrongCast()
    {
        if (version_compare(phpversion() ?: '0.0.0', '8.0.0-dev', '>=')) {
            $this->markTestSkipped('settype() can no longer be muted since PHP 8.');
        }

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Cannot cast arrrg to special');

        $command = new DemoCli();

        $command->getParameterValue(
            'arrrg',
            [
                'type'   => 'special',
                'values' => null,
            ]
        );
    }

    /**
     * @covers ::getParameterValue
     */
    public function testGetParameterValueWrongValue()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(
            "The parameter myProp must be one of the following values: [42.5, 1]; '42' given."
        );

        $command = new DemoCli();

        $command->getParameterValue(
            '42',
            [
                'property' => 'myProp',
                'type'     => 'float',
                'values'   => '42.5, 1',
            ]
        );
    }
}
