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
use Railt\Lexer\LexerInterface;
use Railt\Tests\Lexer\Impl\GraphQLLexerBuilder;
use Railt\Tests\Lexer\Impl\JsonLexerBuilder;
use Railt\Tests\Lexer\Impl\PP2LexerBuilder;
use Railt\Tests\Lexer\Impl\SDLLexerBuilder;

/**
 * Class LexersTestCase
 */
class LexersTestCase extends TestCase
{
    /**
     * @var array
     */
    private static $benchmarks = [];

    /**
     * @var array
     */
    private const SAMPLES = [
        'GraphQL + SDL'     => [GraphQLLexerBuilder::class, __DIR__ . '/resources/graphql/*.graphqls'],
        'GraphQL + Queries' => [GraphQLLexerBuilder::class, __DIR__ . '/resources/graphql/*.graphql'],
        'Json'              => [JsonLexerBuilder::class, __DIR__ . '/resources/json/*.json'],
        'SDL'               => [SDLLexerBuilder::class, __DIR__ . '/resources/sdl/*.graphqls'],
        'PP2'               => [PP2LexerBuilder::class, __DIR__ . '/resources/pp2/*.pp2'],
    ];

    /**
     * @return array
     * @throws \Railt\Io\Exception\NotReadableException
     */
    public function provider(): array
    {
        $result = [];

        foreach (self::SAMPLES as $name => [$builder, $files]) {
            foreach (\glob($files) as $file) {
                if (\substr_count(\basename($file), '.') > 1) {
                    continue;
                }

                $result[$name . ' < ' . \basename($file)] = [(new $builder())->getLexer(), File::fromPathname($file)];
            }
        }

        return $result;
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     * @param Readable $file
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testCompareTokens(LexerInterface $lexer, Readable $file): void
    {
        $bench = \microtime(true);

        $haystack = [];

        foreach ($lexer->lex($file) as $token) {
            if ($token->getName() === LexerInterface::T_UNKNOWN) {
                $this->fail('Unknown token ' . (string)$token);
            }

            $haystack[] = [$token->getName(), $token->getValue(), $token->getOffset()];
        }

        $json = \json_encode($haystack);

        $out = $file->getPathname() . '.json';

        if (! \is_file($out)) {
            \file_put_contents($out, $json);
        }

        $this->assertStringEqualsFile($out, $json);

        $name = \basename(\dirname($file->getPathname())) . '/' . \basename($file->getPathname());
        self::$benchmarks[$name] = \round((\microtime(true) - $bench) * 1000) . 'ms';
    }

    /**
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        $out = __DIR__ . '/' . \microtime(true) . '.bench.json';

        \file_put_contents($out, \json_encode(self::$benchmarks, \JSON_PRETTY_PRINT));
    }
}
