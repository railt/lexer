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
use Railt\Lexer\Driver\Common\PCRECompiler;
use Railt\Lexer\Stateless;
use Railt\Lexer\TokenInterface;

/**
 * Class NativeStateless
 */
class NativeStateless extends Lexer implements Stateless
{
    /**
     * @var PCRECompiler
     */
    private $pcre;

    /**
     * NativeStateless constructor.
     */
    public function __construct()
    {
        $this->pcre = new PCRECompiler();
    }

    /**
     * @param string $name
     * @param string $pcre
     * @param bool $skip
     * @return Stateless
     */
    public function add(string $name, string $pcre, bool $skip = false): Stateless
    {
        $this->pcre->add($name, $pcre);

        if ($skip) {
            $this->skip[] = $name;
        }

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->pcre->getTokens());
    }

    /**
     * @return iterable
     */
    public function getDefinedTokens(): iterable
    {
        return $this->pcre->getTokens();
    }

    /**
     * @param Readable $file
     * @return \Traversable|TokenInterface
     */
    protected function exec(Readable $file): \Traversable
    {
        $lexer = new NativeStateful($this->pcre->compile(), $this->skip);

        return $lexer->lex($file, true);
    }
}
