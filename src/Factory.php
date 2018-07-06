<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer;

use Parle\Lexer as Parle;
use Railt\Lexer\Driver\NativeRegex;
use Railt\Lexer\Driver\ParleLexer;

/**
 * The `Factory` class exists as a convenient way to
 * pick the best available lexer implementation.
 */
final class Factory
{
    /**
     * Creates a new lexer instance
     *
     * ```php
     * $lexer = Railt\Lexer\Factory::create();
     * ```
     *
     * This method always returns an instance implementing `LexerInterface` interface,
     * the actual lexer implementation is an implementation detail.
     *
     * This method should usually only be called once at the beginning of the program.
     *
     * @param array $tokens
     * @param array $skip
     * @return LexerInterface
     * @throws Exception\BadLexemeException
     */
    public static function create(array $tokens = [], array $skip = []): LexerInterface
    {
        if (\class_exists(Parle::class, false)) {
            return new ParleLexer($tokens, $skip);
        }

        return new NativeRegex($tokens, $skip);
        // @codeCoverageIgnoreEnd
    }
}
