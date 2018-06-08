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
use Railt\Io\Readable;
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
    private const TEMPLATE =
        '
%s:
| Sample        | %s (%d tokens)
| Time          | %01.5fs
| AVG           | %01.5fs
| Token/s       | %d
';

    /**
     * @param LexerInterface $lexer
     * @param array $results
     * @param int $tokens
     * @param Readable $sources
     */
    private function write(LexerInterface $lexer, array $results, int $tokens, Readable $sources): void
    {
        $sum = \array_sum($results);
        $avg = $sum / \count($results);

        echo \vsprintf(self::TEMPLATE, [
            \basename(\str_replace('\\', '/', \get_class($lexer))),
            \basename($sources->getPathname()),
            $tokens / \count($results),
            $sum,                       /* SUM */
            $avg,                       /* AVG */
            $tokens / $sum              /* TPS */
        ]);
        \flush();
    }

    /**
     * @return array
     */
    public function benchesProvider(): array
    {
        return [
            [1000, File::fromPathname(__DIR__ . '/resources/little.txt')],
            [100, File::fromPathname(__DIR__ . '/resources/average.txt')],
            [10, File::fromPathname(__DIR__ . '/resources/large.txt')],
        ];
    }

    /**
     * @dataProvider benchesProvider
     * @param int $samples
     * @param Readable $sources
     */
    public function testParleLexer(int $samples, Readable $sources): void
    {
        $tokens = require __DIR__ . '/resources/graphql.lex.php';
        $lexer = new ParleStateless();

        foreach ($tokens as $token => $pcre) {
            $lexer->add($token, $pcre);
        }

        $this->execute($lexer, $samples, $sources);
    }

    /**
     * @dataProvider benchesProvider
     * @param int $samples
     * @param Readable $sources
     */
    public function testNativeStatefulLexer(int $samples, Readable $sources): void
    {
        $tokens = require __DIR__ . '/resources/graphql.lex.php';
        $compiler = new PCRECompiler();

        foreach ($tokens as $token => $pcre) {
            $compiler->add($token, $pcre);
        }
        $lexer = new NativeStateful($compiler->compile(), []);

        $this->execute($lexer, $samples, $sources);
    }

    /**
     * @dataProvider benchesProvider
     * @param int $samples
     * @param Readable $sources
     */
    public function testNativeStatelessLexer(int $samples, Readable $sources): void
    {
        $tokens = require __DIR__ . '/resources/graphql.lex.php';
        $lexer = new NativeStateless();

        foreach ($tokens as $token => $pcre) {
            $lexer->add($token, $pcre);
        }

        $this->execute($lexer, $samples, $sources);
    }

    /**
     * @param int $samples
     * @param LexerInterface $lexer
     * @param Readable $sources
     */
    private function execute(LexerInterface $lexer, int $samples, Readable $sources): void
    {
        $cnt = 0;
        $results = [];

        for ($i = 0; $i < $samples; ++$i) {
            $start = \microtime(true);

            $cnt += \count(\iterator_to_array($lexer->lex($sources)));

            $results[] = \microtime(true) - $start;
        }

        $this->write($lexer, $results, $cnt, $sources);
        $this->assertTrue(true);
    }
}
