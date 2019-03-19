<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Lexer\Impl;

use Railt\Lexer\Builder;
use Railt\Lexer\Builder\ProvidesLexer;
use Railt\Lexer\LexerInterface;

/**
 * Class JsonLexerBuilder
 */
class JsonLexerBuilder implements ProvidesLexer
{
    /**
     * @var string[]
     */
    private const TOKENS = [
        'skip'     => '\s',
        'true'     => 'true',
        'false'    => 'false',
        'null'     => 'null',
        'string'   => '"[^"\\\]*(\\\.[^"\\\]*)*"',
        'brace_'   => '{',
        '_brace'   => '}',
        'bracket_' => '\[',
        '_bracket' => '\]',
        'colon'    => ':',
        'comma'    => ',',
        'number'   => '\d+',
    ];

    /**
     * @var string[]
     */
    private const SKIP = ['skip'];

    /**
     * @return LexerInterface
     */
    public function getLexer(): LexerInterface
    {
        $builder = new Builder();

        foreach (self::TOKENS as $name => $pattern) {
            $builder->add($name, $pattern);
        }

        foreach (self::SKIP as $name) {
            $builder->skip($name);
        }

        return $builder->build();
    }
}
