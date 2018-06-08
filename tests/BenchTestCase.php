<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Lexer;

use Railt\Io\File;
use Railt\Lexer\Driver\Common\PCRECompiler;
use Railt\Lexer\Driver\NativeStateful;
use Railt\Lexer\Driver\NativeStateless;
use Railt\Lexer\Driver\ParleStateless;
use Railt\Lexer\LexerInterface;

/**
 * Class BenchTestCase
 */
class BenchTestCase extends BaseTestCase
{
    /**
     * @return array
     */
    public function provider(): array
    {
        $tokens = require __DIR__ . '/resources/graphql.lex.php';

        $result = [];

        /**
         * Librtl
         */
        $lexer = new ParleStateless();
        foreach ($tokens as $token => $pcre) {
            $lexer->add($token, $pcre);
        }
        $result[] = [$lexer];

        /**
         * Native (Stateless)
         */
        $lexer = new NativeStateless();
        foreach ($tokens as $token => $pcre) {
            $lexer->add($token, $pcre);
        }
        $result[] = [$lexer];

        /**
         * Native stateful
         */
        $compiler = new PCRECompiler();
        foreach ($tokens as $token => $pcre) {
            $compiler->add($token, $pcre);
        }
        $lexer = new NativeStateful($compiler->compile(), []);
        $result[] = [$lexer];

        return $result;
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testLittleBench(LexerInterface $lexer): void
    {
        $results = [];
        $sources = File::fromPathname(__DIR__ . '/resources/little.txt');

        for ($i = 0; $i < 1000; ++$i) {
            $start = \microtime(true);

            \iterator_to_array($lexer->lex($sources));

            $results[] = \microtime(true) - $start;
        }

        $avg = \array_sum($results) / \count($results);
        $avg = \number_format($avg, 5);

        echo \vsprintf('%s %s: avg %sms, iter %d' . "\n", [
            __FUNCTION__,
            \get_class($lexer),
            $avg,
            \count($results)
        ]);
        \flush();

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testAverageBench(LexerInterface $lexer): void
    {
        $results = [];
        $sources = File::fromPathname(__DIR__ . '/resources/average.txt');

        for ($i = 0; $i < 100; ++$i) {
            $start = \microtime(true);

            \iterator_to_array($lexer->lex($sources));

            $results[] = \microtime(true) - $start;
        }

        $avg = \array_sum($results) / \count($results);
        $avg = \number_format($avg, 5);

        echo \vsprintf('%s %s: avg %sms, iter %d' . "\n", [
            __FUNCTION__,
            \get_class($lexer),
            $avg,
            \count($results)
        ]);
        \flush();

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testLargeBench(LexerInterface $lexer): void
    {
        $results = [];
        $sources = File::fromPathname(__DIR__ . '/resources/large.txt');

        for ($i = 0; $i < 10; ++$i) {
            $start = \microtime(true);

            \iterator_to_array($lexer->lex($sources));

            $results[] = \microtime(true) - $start;
        }

        $avg = \array_sum($results) / \count($results);
        $avg = \number_format($avg, 5);

        echo \vsprintf('%s %s: avg %sms, iter %d' . "\n", [
            __FUNCTION__,
            \get_class($lexer),
            $avg,
            \count($results)
        ]);
        \flush();

        $this->assertTrue(true);
    }
}
