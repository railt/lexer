<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Runtime;

use Railt\Lexer\Token\EndOfInput;

/**
 * An iterator which returns a list of regex groups
 */
class RegexIterator
{
    /**
     * @var int
     */
    public const TOKEN_NAME = 0x00;

    /**
     * @var int
     */
    public const TOKEN_VALUE = 0x01;

    /**
     * @var int
     */
    public const TOKEN_OFFSET = 0x02;

    /**
     * @var string
     */
    private $pattern;

    /**
     * RegexIterator constructor.
     *
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param string $subject
     * @param int $offset
     * @return \Traversable
     * @throws \Railt\Lexer\Exception\RegularExpressionException
     */
    public function lex(string $subject, int $offset = 0): \Traversable
    {
        $size = \strlen($subject);
        $status = \preg_match_all($this->pattern, $subject, $matches, \PREG_SET_ORDER, $offset);

        \assert(Validator::assert($status, \preg_last_error()));

        foreach ($matches as $match) {
            yield [
                static::TOKEN_NAME   => $match['MARK'],
                static::TOKEN_VALUE  => $match[0],
                static::TOKEN_OFFSET => $offset,
            ];

            $offset += \strlen($match[0]);
        }

        if ($offset === $size) {
            yield [
                static::TOKEN_NAME   => EndOfInput::T_NAME,
                static::TOKEN_VALUE  => "\0",
                static::TOKEN_OFFSET => $offset,
            ];
        }
    }
}
