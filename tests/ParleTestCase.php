<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Lexer;

use Railt\Lexer\Driver\ParleStateless;
use Railt\Lexer\LexerInterface;

/**
 * Class ParleTestCase
 */
class ParleTestCase extends LexerTestCase
{
    /**
     * @return array
     */
    public function provider(): array
    {
        $lexer = new ParleStateless();

        $lexer->add('T_WHITESPACE', '\s+', true);
        $lexer->add('T_DIGIT', '\d+');

        return [[$lexer]];
    }
}
