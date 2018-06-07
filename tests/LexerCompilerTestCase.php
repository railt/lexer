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
 * Class LexerCompiler
 */
abstract class LexerCompilerTestCase extends BaseTestCase
{
    /**
     * @return Stateless
     */
    abstract protected function mock(): Stateless;

    /**
     * @return array
     */
    public function provider(): array
    {
        $mock = $this->mock();

        $mock->add('T_WHITESPACE', '\s+', true);
        $mock->add('T_DIGIT', '\d+');

        return [[$mock]];
    }

    /**
     * @dataProvider provider
     * @param Stateless $stateless
     */
    public function testCompilable(Stateless $stateless): void
    {

    }
}
