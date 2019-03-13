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
use Railt\Lexer\Token\EndOfInput;

/**
 * Interface LexerInterface
 */
interface LexerInterface
{
    /**
     * @var string
     */
    public const T_UNKNOWN = 'T_UNKNOWN';

    /**
     * @var string
     */
    public const T_EOI = 'T_EOI';

    /**
     * Returns a tokens stream from the given source file.
     *
     * @param Readable $input
     * @return iterable|TokenInterface[]
     */
    public function lex(Readable $input): iterable;
}
