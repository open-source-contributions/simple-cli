<?php

namespace Tests\SimpleCli;

use ArrayIterator;
use SimpleCli\WordsList;
use Traversable;

/**
 * @coversDefaultClass \SimpleCli\WordsList
 */
class WordsListTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getWords
     */
    public function testConstructor()
    {
        $words = ['ab', 'bc', 'cd'];
        $wordsList = new WordsList($words);

        self::assertSame($words, $wordsList->getWords());
    }

    /**
     * @covers ::getArrayIterator
     */
    public function testGetArrayIterator()
    {
        $words = ['ab', 'bc', 'cd'];
        $list = (new WordsList($words))->getArrayIterator();

        self::assertInstanceOf(ArrayIterator::class, $list);
        self::assertSame($words, iterator_to_array($list));
    }

    /**
     * @covers ::getIterator
     */
    public function testGetIterator()
    {
        $words = ['ab', 'bc', 'cd'];
        $list = new WordsList($words);

        self::assertInstanceOf(Traversable::class, $list);
        self::assertSame($words, iterator_to_array($list));
    }

    /**
     * @covers ::findClosestWords
     * @covers ::getWordScore
     */
    public function testFindClosestWords()
    {
        $list = new WordsList(['ab', 'bc', 'cd']);

        self::assertSame([], $list->findClosestWords('ef'));

        $list = new WordsList(['update', 'delete', 'create']);

        self::assertSame(['update'], $list->findClosestWords('upgrade'));

        self::assertSame(['update', 'create'], $list->findClosestWords('date'));

        self::assertSame(['update', 'create', 'delete'], $list->findClosestWords('date', 1));
    }

    /**
     * @covers ::findClosestWord
     * @covers ::getWordScore
     */
    public function testFindClosestWord()
    {
        $list = new WordsList(['ab', 'bc', 'cd']);

        self::assertNull($list->findClosestWord('ef'));

        $list = new WordsList(['update', 'delete', 'create']);

        self::assertSame('update', $list->findClosestWord('upgrade'));

        self::assertSame('update', $list->findClosestWord('date'));

        self::assertSame('update', $list->findClosestWord('date', 1));

        self::assertNull($list->findClosestWord('date', 5));

        self::assertSame('update', $list->findClosestWord('dateup', 5));

        self::assertSame('delete', $list->findClosestWord('dellete'));
    }
}
