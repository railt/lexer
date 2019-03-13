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
 * The lexical token that returns from stream.
 */
interface TokenInterface
{
    /**
     * @var string
     */
    public const DEFAULT_TOKEN_STATE = 'default';

    /**
     * Returns the namespace (scope) of the token.
     *
     * @return string
     */
    public function getState(): string;

    /**
     * Returns the name of the token.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Token position in bytes relative to the beginning of the source.
     *
     * @return int
     */
    public function getOffset(): int;

    /**
     * Returns the captured value.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Returns the token value size in bytes.
     *
     * @return int
     */
    public function getBytes(): int;
}
