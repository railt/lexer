<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Token;

use Railt\Lexer\LexerInterface;

/**
 * Class Eoi
 */
final class EndOfInput extends Token
{
    /**
     * End of input token name
     */
    public const T_NAME = LexerInterface::T_EOI;

    /**
     * Eoi constructor.
     *
     * @param int $offset
     * @param string|null $state
     */
    public function __construct(int $offset, string $state = null)
    {
        parent::__construct(self::T_NAME, "\0", $offset, $state);
    }

    /**
     * @return int
     */
    public function getBytes(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('"%s" (%s)', '\0', self::T_NAME);
    }
}
