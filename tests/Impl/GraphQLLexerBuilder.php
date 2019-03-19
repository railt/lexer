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
 * Class GraphQLLexerBuilder
 */
class GraphQLLexerBuilder implements ProvidesLexer
{
    /**
     * @var string[]
     */
    private const TOKENS = [
        'T_AND'               => '&',
        'T_OR'                => '\\|',
        'T_PARENTHESIS_OPEN'  => '\\(',
        'T_PARENTHESIS_CLOSE' => '\\)',
        'T_BRACKET_OPEN'      => '\\[',
        'T_BRACKET_CLOSE'     => '\\]',
        'T_BRACE_OPEN'        => '{',
        'T_BRACE_CLOSE'       => '}',
        'T_NON_NULL'          => '!',
        'T_THREE_DOTS'        => '\\.\\.\\.',
        'T_EQUAL'             => '=',
        'T_DIRECTIVE_AT'      => '@',
        'T_COLON'             => ':',
        'T_COMMA'             => ',',
        'T_HEX_NUMBER'        => '\\-?0x([0-9a-fA-F]+)',
        'T_BIN_NUMBER'        => '\\-?0b([0-1]+)',
        'T_NUMBER'            => '\\-?(?:0|[1-9][0-9]*)(?:\\.[0-9]+)?(?:[eE][\\+\\-]?[0-9]+)?',
        'T_TRUE'              => '(?<=\\b)true\\b',
        'T_FALSE'             => '(?<=\\b)false\\b',
        'T_NULL'              => '(?<=\\b)null\\b',
        'T_BLOCK_STRING'      => '"""((?:\\\\"""|(?!""").)*)"""',
        'T_STRING'            => '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
        'T_TYPE'              => '(?<=\\b)type\\b',
        'T_ENUM'              => '(?<=\\b)enum\\b',
        'T_UNION'             => '(?<=\\b)union\\b',
        'T_INTERFACE'         => '(?<=\\b)interface\\b',
        'T_SCHEMA'            => '(?<=\\b)schema\\b',
        'T_SCALAR'            => '(?<=\\b)scalar\\b',
        'T_DIRECTIVE'         => '(?<=\\b)directive\\b',
        'T_INPUT'             => '(?<=\\b)input\\b',
        'T_QUERY'             => '(?<=\\b)query\\b',
        'T_MUTATION'          => '(?<=\\b)mutation\\b',
        'T_SUBSCRIPTION'      => '(?<=\\b)subscription\\b',
        'T_FRAGMENT'          => '(?<=\\b)fragment\\b',
        'T_EXTEND'            => '(?<=\\b)extend\\b',
        'T_EXTENDS'           => '(?<=\\b)extends\\b',
        'T_IMPLEMENTS'        => '(?<=\\b)implements\\b',
        'T_ON'                => '(?<=\\b)on\\b',
        'T_PLUS'              => '\\+',
        'T_MINUS'             => '\\-',
        'T_DIV'               => '/',
        'T_MUL'               => '\\*',
        'T_VARIABLE'          => '\\$([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)',
        'T_NAME'              => '[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*',
        'T_COMMENT'           => '#[^\\n]*',
        'T_HTAB'              => '\\x09',
        'T_LF'                => '\\x0A',
        'T_CR'                => '\\x0D',
        'T_WHITESPACE'        => '\\x20+',
        'T_UTF32BE_BOM'       => '^\\x00\\x00\\xFE\\xFF',
        'T_UTF32LE_BOM'       => '^\\xFE\\xFF\\x00\\x00',
        'T_UTF16BE_BOM'       => '^\\xFE\\xFF',
        'T_UTF16LE_BOM'       => '^\\xFF\\xFE',
        'T_UTF8_BOM'          => '^\\xEF\\xBB\\xBF',
        'T_UTF7_BOM'          => '^\\x2B\\x2F\\x76\\x38\\x2B\\x2F\\x76\\x39\\x2B\\x2F\\x76\\x2B\\x2B\\x2F\\x76\\x2F',
    ];

    /**
     * @var string[]
     */
    private const SKIP = [
        'T_COMMENT',
        'T_HTAB',
        'T_LF',
        'T_CR',
        'T_WHITESPACE',
        'T_UTF32BE_BOM',
        'T_UTF32LE_BOM',
        'T_UTF16BE_BOM',
        'T_UTF16LE_BOM',
        'T_UTF8_BOM',
        'T_UTF7_BOM'
    ];

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
