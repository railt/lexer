<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Tests\Lexer;

use Railt\Io\File;
use Railt\Lexer\LexerInterface;
use Railt\Lexer\Result\Eoi;
use Railt\Lexer\Result\Unknown;
use Railt\Lexer\Stateless;

/**
 * Class LexerCompiler
 */
abstract class LexerTestCase extends BaseTestCase
{
    /**
     * @return array
     */
    abstract public function provider(): array;

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testDigits(LexerInterface $lexer): void
    {
        $result = \iterator_to_array($lexer->lex(File::fromSources('23 42')));

        $this->assertCount(2, $result);
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testDigitsWithEoi(LexerInterface $lexer): void
    {
        $result = \iterator_to_array($lexer->lex(File::fromSources('23 42'), true));

        $this->assertCount(3, $result);
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testUnknownLookahead(LexerInterface $lexer): void
    {
        $file   = File::fromSources("23 \nunknown \n42");
        $result = \iterator_to_array($lexer->lex($file));

        $this->assertCount(3, $result);
        $this->assertEquals('T_DIGIT', $result[0]->name());
        $this->assertEquals('T_UNKNOWN', $result[1]->name());
        $this->assertEquals('T_DIGIT', $result[2]->name());

        /** @var Unknown $unknown */
        $unknown = $result[1];

        $this->assertEquals(4, $unknown->offset(), 'Bad Offset');
        $this->assertEquals(7, $unknown->length(), 'Bad Length');
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testStatelessAddToken(LexerInterface $lexer): void
    {
        if (! ($lexer instanceof Stateless)) {
            $this->markTestSkipped('This test is only available for stateless lexers');
        }

        $before = $this->toArray($lexer->getDefinedTokens());
        $this->assertCount(2, $before);
        $this->assertArrayHasKey('T_DIGIT', $before);
        $this->assertArrayHasKey('T_WHITESPACE', $before);

        $lexer->add('T_WORD', '\w+');

        $after = $this->toArray($lexer->getDefinedTokens());
        $this->assertCount(3, $after);
        $this->assertArrayHasKey('T_DIGIT', $after);
        $this->assertArrayHasKey('T_WHITESPACE', $after);
        $this->assertArrayHasKey('T_WORD', $after);
    }

    /**
     * @param iterable|\Traversable|array $items
     * @return array
     */
    protected function toArray(iterable $items): array
    {
        return \is_array($items) ? $items : \iterator_to_array($items);
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testStatelessLexWithAddedToken(LexerInterface $lexer): void
    {
        if (! ($lexer instanceof Stateless)) {
            $this->markTestSkipped('This test is only available for stateless lexers');
        }

        $lexer->add('T_WORD', '\w+');
        $result = \iterator_to_array($lexer->lex(File::fromSources('23 42 word word')));

        $this->assertCount(4, $result);
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testStatelessAddSkippedToken(LexerInterface $lexer): void
    {
        if (! ($lexer instanceof Stateless)) {
            $this->markTestSkipped('This test is only available for stateless lexers');
        }

        $before = $this->toArray($lexer->getDefinedTokens());
        $this->assertCount(2, $before);
        $this->assertArrayHasKey('T_DIGIT', $before);
        $this->assertArrayHasKey('T_WHITESPACE', $before);

        $lexer->add('T_WORD', '\w+', true);

        $after = $this->toArray($lexer->getDefinedTokens());
        $this->assertCount(3, $after);
        $this->assertArrayHasKey('T_DIGIT', $after);
        $this->assertArrayHasKey('T_WHITESPACE', $after);
        $this->assertArrayHasKey('T_WORD', $after);
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testStatelessLexWithSkippedToken(LexerInterface $lexer): void
    {
        if (! ($lexer instanceof Stateless)) {
            $this->markTestSkipped('This test is only available for stateless lexers');
        }

        $lexer->add('T_WORD', '\w+', true);
        $result = \iterator_to_array($lexer->lex(File::fromSources('23 word word 42')));

        $this->assertCount(2, $result);
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testStatelessCheckAddedToken(LexerInterface $lexer): void
    {
        if (! ($lexer instanceof Stateless)) {
            $this->markTestSkipped('This test is only available for stateless lexers');
        }

        $lexer->add('T_WORD', '\w+');

        $this->assertTrue($lexer->has('T_DIGIT'));
        $this->assertTrue($lexer->has('T_WHITESPACE'));
        $this->assertTrue($lexer->has('T_WORD'));
        $this->assertFalse($lexer->has(Unknown::T_NAME));
        $this->assertFalse($lexer->has(Eoi::T_NAME));
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testStatelessCheckAddedSkippedToken(LexerInterface $lexer): void
    {
        if (! ($lexer instanceof Stateless)) {
            $this->markTestSkipped('This test is only available for stateless lexers');
        }

        $lexer->add('T_WORD', '\w+', true);

        $this->assertTrue($lexer->has('T_DIGIT'));
        $this->assertTrue($lexer->has('T_WHITESPACE'));
        $this->assertTrue($lexer->has('T_WORD'));
        $this->assertFalse($lexer->has(Unknown::T_NAME));
        $this->assertFalse($lexer->has(Eoi::T_NAME));
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testStatelessCheckSkipWhenNotSkipToken(LexerInterface $lexer): void
    {
        if (! ($lexer instanceof Stateless)) {
            $this->markTestSkipped('This test is only available for stateless lexers');
        }

        $lexer->add('T_WORD', '\w+');

        $this->assertTrue($lexer->isSkipped('T_WHITESPACE'));
        $this->assertFalse($lexer->isSkipped('T_DIGIT'));
        $this->assertFalse($lexer->isSkipped('T_WORD'));
    }

    /**
     * @dataProvider provider
     * @param LexerInterface $lexer
     */
    public function testStatelessCheckSkipToken(LexerInterface $lexer): void
    {
        if (! ($lexer instanceof Stateless)) {
            $this->markTestSkipped('This test is only available for stateless lexers');
        }

        $lexer->add('T_WORD', '\w+', true);

        $this->assertTrue($lexer->isSkipped('T_WHITESPACE'));
        $this->assertFalse($lexer->isSkipped('T_DIGIT'));
        $this->assertTrue($lexer->isSkipped('T_WORD'));
    }
}
