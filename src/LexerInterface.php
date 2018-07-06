<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer;

use Railt\Io\Readable;

/**
 * Interface LexerInterface
 */
interface LexerInterface
{
    /**
     * LexerInterface constructor.
     * @param array $tokens
     * @param array $skip
     */
    public function __construct(array $tokens = [], array $skip = []);

    /**
     * Compiling the current state of the lexer and returning stream tokens from the source file
     *
     * @param Readable $input
     * @return \Traversable|TokenInterface[]
     */
    public function lex(Readable $input): \Traversable;

    /**
     * Add a lexer rule
     *
     * @param string $token Token name
     * @param string $pcre Perl compatible regular expression used for token matching
     * @return LexerInterface|$this
     */
    public function add(string $token, string $pcre): self;

    /**
     * A method for marking a token as skipped.
     *
     * @param string $name Token name
     * @return LexerInterface
     */
    public function skip(string $name): self;
}
