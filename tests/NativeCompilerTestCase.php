<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Lexer;

use Railt\Lexer\Stateless;

/**
 * Class NativeCompilerTestCase
 */
class NativeCompilerTestCase extends LexerCompilerTestCase
{
    /**
     * @return Stateless
     */
    protected function mock(): Stateless
    {
        return new NativeStateless();
    }
}
