<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Lexer;

use Railt\Lexer\Driver\Common\PCRECompiler;
use Railt\Lexer\Driver\NativeStateful;

/**
 * Class NativeCompilerTestCase
 */
class NativeStatefulTestCase extends LexerTestCase
{
    /**
     * @return array
     */
    public function provider(): array
    {
        $pattern1 = '/\G(?P<T_WHITESPACE>\s+)|(?P<T_DIGIT>\d+)|(?P<T_UNKNOWN>.*?)/usS';
        $pattern2 = (new PCRECompiler(['T_WHITESPACE' => '\s+', 'T_DIGIT' => '\d+']))->compile();

        return [
            [new NativeStateful($pattern1, ['T_WHITESPACE'])],
            [new NativeStateful($pattern2, ['T_WHITESPACE'])],
        ];
    }
}
