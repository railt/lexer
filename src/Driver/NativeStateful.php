<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Driver;

use Railt\Io\Readable;
use Railt\Lexer\Iterator\RegexNamedGroupsIterator;
use Railt\Lexer\Result\Eoi;
use Railt\Lexer\Result\Token;
use Railt\Lexer\Result\Unknown;
use Railt\Lexer\Stateful;
use Railt\Lexer\TokenInterface;

/**
 * Class NativeStateful
 */
class NativeStateful extends Lexer implements Stateful
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * NativeStateful constructor.
     * @param string $pattern
     * @param array $skipped
     */
    public function __construct(string $pattern, array $skipped = [])
    {
        $this->pattern = $pattern;
        $this->skip    = $skipped;
    }

    /**
     * @param Readable $file
     * @return \Traversable
     */
    protected function exec(Readable $file): \Traversable
    {
        $offset = 0;
        $regex  = new RegexNamedGroupsIterator($this->pattern, $file->getContents());

        $iterator = $regex->getIterator();

        while ($iterator->valid()) {
            /** @var TokenInterface $token */
            $token = $iterator->key() === Unknown::T_NAME
                ? $this->unknown($iterator, $offset)
                : $this->token($iterator, $offset);

            $offset += $token->bytes();

            yield $token;
        }

        yield new Eoi($offset);
    }

    /**
     * @param \Traversable|\Generator $iterator
     * @param int $offset
     * @return Unknown
     */
    private function unknown(\Traversable $iterator, int $offset): TokenInterface
    {
        $body = $iterator->current()[0];
        $iterator->next();

        $body .= $this->reduce($iterator, Unknown::T_NAME);

        return new Unknown($body, $offset);
    }

    /**
     * @param \Traversable|\Iterator $iterator
     * @param string $key
     * @return string
     */
    protected function reduce(\Traversable $iterator, string $key): string
    {
        $body = '';

        while ($iterator->valid()) {
            if ($iterator->key() !== $key) {
                break;
            }

            $body .= $iterator->current()[0];

            $iterator->next();
        }

        return $body;
    }

    /**
     * @param \Traversable|\Iterator $iterator
     * @param int $offset
     * @return Token
     */
    private function token(\Traversable $iterator, int $offset): TokenInterface
    {
        [$name, $context] = [$iterator->key(), $iterator->current()];

        $iterator->next();

        return new Token($name, $context, $offset);
    }
}
