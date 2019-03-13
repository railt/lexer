<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer;

use Railt\Lexer\Builder\ConfigurationTrait;
use Railt\Lexer\Builder\DefinitionInterface;
use Railt\Lexer\Builder\Facade;
use Railt\Lexer\Builder\TokenDefinition;

/**
 * Class Builder
 */
class Builder implements BuilderInterface
{
    use ConfigurationTrait;

    /**
     * @var array|TokenDefinition[]
     */
    private $tokens = [];

    /**
     * Builder constructor.
     *
     * @param int|null $options
     */
    public function __construct(int $options = null)
    {
        if ($options !== null) {
            $this->options = $options;
        }
    }

    /**
     * @param string $token
     * @param string $pattern
     * @param string|null $state
     * @param string|null $then
     * @return DefinitionInterface|TokenDefinition
     */
    public function add(string $token, string $pattern, string $state = null, string $then = null): DefinitionInterface
    {
        return $this->tokens[] = (new TokenDefinition($token, $pattern))->in($state)->then($then);
    }

    /**
     * @return iterable|DefinitionInterface[]
     */
    public function all(): iterable
    {
        return $this->tokens;
    }

    /**
     * @param string $initial
     * @return LexerInterface
     */
    public function build(string $initial = DefinitionInterface::DEFAULT_STATE): LexerInterface
    {
        $factory = new Facade($this, $this->tokens);

        return new Lexer($factory->getPatterns(), $factory->getJumps(), $initial);
    }
}
