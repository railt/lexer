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
     * @param \Generator $coroutine
     * @param int $offset
     * @throws \Railt\Lexer\Exception\RegularExpressionException
     */
    public function lex(string $subject, \Generator $coroutine, int $offset = 0): void
    {
        $size = \strlen($subject);

        $executor = function (array $matches) use ($coroutine, &$offset, $size): void {
            if (! $coroutine->valid()) {
                return;
            }

            foreach (\array_reverse($matches) as $name => $value) {
                if (\is_string($name) && ($length = \strlen($value)) > 0) {
                    $data = [
                        static::TOKEN_NAME   => $name,
                        static::TOKEN_VALUE  => $value,
                        static::TOKEN_OFFSET => $offset,
                    ];

                    $coroutine->send($data);

                    $offset += $length;
                }
            }

            if ($offset === $size) {
                $this->complete($coroutine, $offset);
            }
        };

        $status = @\preg_replace_callback($this->pattern, $executor, \substr($subject, $offset));

        \assert(Validator::assert($status, \preg_last_error()));
    }

    /**
     * @param \Generator $coroutine
     * @param int $offset
     */
    private function complete(\Generator $coroutine, int $offset): void
    {
        $coroutine->send([
            static::TOKEN_NAME   => EndOfInput::T_NAME,
            static::TOKEN_VALUE  => "\0",
            static::TOKEN_OFFSET => $offset,
        ]);
    }
}
