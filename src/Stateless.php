<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer;

/**
 * Interface Stateless
 */
interface Stateless extends Stateful
{
    /**
     * Add a lexer rule
     *
     * @param string $token Token name
     * @param string $pcre Perl compatible regular expression used for token matching
     * @param bool $skip Specifying the token that should be ignored on return
     * @return Stateless|$this
     */
    public function add(string $token, string $pcre, bool $skip = false): self;

    /**
     * Returns a list of defined tokens in format:
     * <code>
     * [
     *      'TOKEN_NAME' => /PCRE/,
     *      'TOKEN_NAME_2' => /PCRE_2/,
     * ]
     * </code>
     *
     * @return iterable
     */
    public function getDefinedTokens(): iterable;

    /**
     * @param string $name
     * @return bool
     */
    public function isSkipped(string $name): bool;

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}
