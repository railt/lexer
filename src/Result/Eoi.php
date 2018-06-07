<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Result;

use Railt\Lexer\TokenInterface;

/**
 * Class Eoi
 */
final class Eoi extends BaseToken
{
    /**
     * End of input token name
     */
    public const T_NAME = 'T_EOI';

    /**
     * @var int
     */
    private $offset;

    /**
     * Eoi constructor.
     * @param int $offset
     */
    public function __construct(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function offset(): int
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return static::T_NAME;
    }

    /**
     * @param int|null $offset
     * @return string
     */
    public function value(int $offset = null): string
    {
        return "\0";
    }

    /**
     * @return int
     */
    public function bytes(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return 0;
    }
}
