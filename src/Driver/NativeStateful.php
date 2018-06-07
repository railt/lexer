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
class NativeStateful implements Stateful
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var array
     */
    protected $skipped;

    /**
     * NativeStateful constructor.
     * @param string $pattern
     * @param array $skipped
     */
    public function __construct(string $pattern, array $skipped = [])
    {
        $this->pattern = $pattern;
        $this->skipped = $skipped;
    }

    /**
     * @param Readable $input
     * @return \Traversable
     */
    public function lex(Readable $input): \Traversable
    {
        foreach ($this->exec($this->pattern, $input->getContents()) as $token) {
            if (! \in_array($token->name(), $this->skipped, true)) {
                yield $token;
            }
        }
    }

    /**
     * @param string $pattern
     * @param string $content
     * @return \Traversable|TokenInterface[]
     */
    protected function exec(string $pattern, string $content): \Traversable
    {
        $offset = 0;
        $regex  = new RegexNamedGroupsIterator($pattern, $content);

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

        $body .= $this->collapse($iterator, Unknown::T_NAME);

        return new Unknown($body, $offset);
    }

    /**
     * @param \Traversable|\Iterator $iterator
     * @param string $token
     * @return string
     */
    private function collapse(\Traversable $iterator, string $token): string
    {
        $body = '';

        while ($iterator->valid()) {
            if ($iterator->key() !== $token) {
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
