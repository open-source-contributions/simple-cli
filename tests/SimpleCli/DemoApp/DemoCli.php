<?php

namespace Tests\SimpleCli\DemoApp;

use SimpleCli\SimpleCli;
use SimpleCli\SimpleCliCommand\Create;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class DemoCli extends SimpleCli
{
    protected $escapeCharacter = '[ESCAPE]';

    protected $readlineFunction = [self::class, 'ask'];

    protected $readlineCompletionRegisterFunction = [self::class, 'register'];

    protected $readlineCompletionExtensions = [];

    public static $registered = [];

    public static $answerer = null;

    public function getCommands(): array
    {
        return [
            'all'    => ArrayRestCommand::class,
            'hall'   => HelpedArrayRestCommand::class,
            'bad'    => BadCommand::class,
            'create' => Create::class,
            'rest'   => RestCommand::class,
            'foobar' => DemoCommand::class,
        ];
    }

    /**
     * @suppressWarnings(PHPMD.UndefinedVariable)
     *
     * @param callable $callback
     */
    public static function register($callback)
    {
        static::$registered[] = $callback;
    }

    public static function ask($question)
    {
        return (static::$answerer)($question);
    }

    public function setAnswerer($answerer)
    {
        static::$answerer = $answerer;
    }

    /**
     * @param array $readlineCompletionExtensions
     */
    public function setReadlineCompletionExtensions(array $readlineCompletionExtensions): void
    {
        $this->readlineCompletionExtensions = $readlineCompletionExtensions;
    }
}
