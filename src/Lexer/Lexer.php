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
use Railt\Lexer\Builder\DefinitionInterface;
use Railt\Lexer\Exception\LogicException;
use Railt\Lexer\Exception\RegularExpressionException;
use Railt\Lexer\Exception\RuntimeException;
use Railt\Lexer\Exception\UnrecognizedStateException;
use Railt\Lexer\Runtime\RegexIterator;
use Railt\Lexer\Token\EndOfInput;
use Railt\Lexer\Token\Token;
use Railt\Lexer\Token\Unknown;

/**
 * Class Lexer
 */
class Lexer implements LexerInterface
{
    /**
     * @var array|RegexIterator[]
     */
    private $states;

    /**
     * @var string
     */
    private $initial;

    /**
     * @var array
     */
    private $jumps;

    /**
     * Lexer constructor.
     *
     * @param array $patterns
     * @param array $jumps
     * @param string|null $initial
     */
    public function __construct(array $patterns, array $jumps, string $initial = null)
    {
        $this->states  = $this->bootRegexIterators($patterns);
        $this->initial = $initial ?? DefinitionInterface::DEFAULT_STATE;
        $this->jumps   = $jumps;
    }

    /**
     * @param array $patterns
     * @return array
     */
    private function bootRegexIterators(array $patterns): array
    {
        $result = [];

        foreach ($patterns as $state => $pattern) {
            $result[$state] = new RegexIterator($pattern);
        }

        return $result;
    }

    /**
     * @param Readable $input
     * @return iterable|TokenInterface[]
     * @throws RuntimeException
     * @throws LogicException
     */
    public function lex(Readable $input): iterable
    {
        return $this->execute($input, $this->initial);
    }

    /**
     * @param Readable $input
     * @param string $state
     * @return \Generator|TokenInterface[]
     * @throws RegularExpressionException
     * @throws UnrecognizedStateException
     */
    private function execute(Readable $input, string $state): \Generator
    {
        [$offset, $value] = [0, ''];

        while (true) {
            $current = $state;
            $buffer  = $this->lexState($state, $input, $offset);

            // @formatter:off
            foreach ($buffer as [
                RegexIterator::TOKEN_NAME   => $name,
                RegexIterator::TOKEN_VALUE  => $value,
                RegexIterator::TOKEN_OFFSET => $offset,
            ]) {
                // @formatter:on
                switch ($name) {
                    case EndOfInput::T_NAME:
                        yield new EndOfInput($offset, $current);
                        break 3;

                    case Unknown::T_NAME:
                        yield new Unknown($value, $offset, $current);
                        break;

                    default:
                        yield new Token($name, $value, $offset, $current);
                        break;
                }
            }

            $offset += \strlen($value);
        }
    }

    /**
     * @param string $state
     * @param Readable $input
     * @param int $offset
     * @return array
     * @throws RegularExpressionException
     * @throws UnrecognizedStateException
     */
    private function lexState(string &$state, Readable $input, int $offset = 0): array
    {
        $buffer = [];

        if (! isset($this->states[$state])) {
            $error = \sprintf('Unrecognized state "%s", therefore the transition is invalid', $state);

            $exception = new UnrecognizedStateException($error);
            $exception->throwsIn($input, $offset);

            throw $exception;
        }

        $iterator = $this->states[$state];

        foreach ($iterator->lex($input->getContents(), $offset) as $data) {
            [$name] = $buffer[] = $data;

            if ($name === LexerInterface::T_EOI) {
                break;
            }

            $next = $this->jumps[$state][$name] ?? null;

            if (\is_string($next)) {
                $state = $next;
                break;
            }
        }

        return $buffer;
    }
}
