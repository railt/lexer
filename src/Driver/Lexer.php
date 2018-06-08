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
use Railt\Lexer\LexerInterface;
use Railt\Lexer\Result\Eoi;
use Railt\Lexer\TokenInterface;

/**
 * Class BaseLexer
 */
abstract class Lexer implements LexerInterface
{
    /**
     * @var array
     */
    protected $skip = [];

    /**
     * @param TokenInterface $token
     * @param bool $eoi
     * @return bool
     */
    protected function shouldKeep(TokenInterface $token, bool $eoi): bool
    {
        $isSkipped = \in_array($token->name(), $this->skip, true);

        $allowsEoi = ($token instanceof Eoi && $eoi) || ! ($token instanceof Eoi);

        return ! $isSkipped && $allowsEoi;
    }

    /**
     * @param Readable $input
     * @param bool $eoi
     * @return \Traversable|TokenInterface[]
     */
    public function lex(Readable $input, bool $eoi = false): \Traversable
    {
        foreach ($this->exec($input) as $token) {
            if ($this->shouldKeep($token, $eoi)) {
                yield $token;
            }
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isSkipped(string $name): bool
    {
        return \in_array($name, $this->skip, true);
    }

    /**
     * @param Readable $file
     * @return \Traversable|TokenInterface
     */
    abstract protected function exec(Readable $file): \Traversable;
}
