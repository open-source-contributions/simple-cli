<?php

namespace Tests\SimpleCli\DemoApp;

use SimpleCli\SimpleCli;
use stdClass;

class BadCli extends SimpleCli
{
    protected $escapeCharacter = '[ESCAPE]';

    public function getCommands(): array
    {
        return [
            'bad' => stdClass::class,
        ];
    }
}
