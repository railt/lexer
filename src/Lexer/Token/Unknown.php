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
 * Class Unknown
 */
final class Unknown extends Token
{
    /**
     * Unknown token name.
     */
    public const T_NAME = LexerInterface::T_UNKNOWN;

    /**
     * Undefined token constructor.
     *
     * @param string $value
     * @param int $offset
     * @param string|null $state
     */
    public function __construct(string $value, int $offset = 0, string $state = null)
    {
        parent::__construct(static::T_NAME, $value, $offset, $state);
    }
}
