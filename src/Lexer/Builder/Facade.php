<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Builder;

/**
 * Class Facade
 */
class Facade
{
    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * @var array|TokenDefinition[][]
     */
    private $states = [];

    /**
     * @var array|string[]|null
     */
    private $patterns;

    /**
     * @var array|array[]
     */
    private $jumps = [];

    /**
     * PatternsBuilder constructor.
     *
     * @param ConfigurationInterface $config
     * @param array|DefinitionInterface[] $tokens
     */
    public function __construct(ConfigurationInterface $config, array $tokens = [])
    {
        $this->config = $config;

        foreach ($tokens as $token) {
            $this->add($token);
        }
    }

    /**
     * @param DefinitionInterface $token
     * @return Facade|$this
     */
    public function add(DefinitionInterface $token): self
    {
        $state = $token->getState();

        if (! isset($this->states[$state])) {
            $this->states[$state] = [];
        }

        $this->states[$state][] = $token;

        return $this;
    }

    /**
     * @param string $name
     * @param array|DefinitionInterface[] $state
     * @return string
     */
    private function compileState(string $name, array $state): string
    {
        $this->jumps[$name] = [];

        $compiler = new PCREBuilder($this->config);

        foreach ($state as $definition) {
            if ($definition->isStateWillChanged()) {
                $this->jumps[$name][$definition->getName()] = $definition->getNextState();
            }

            $compiler->add($definition->getName(), $definition->getPattern());
        }

        return $compiler->compile();
    }

    /**
     * @return void
     */
    private function compile(): void
    {
        if ($this->patterns === null) {
            $this->patterns = [];

            foreach ($this->states as $name => $state) {
                $this->patterns[$name] = $this->compileState($name, $state);
            }
        }
    }

    /**
     * @return array|string[]
     */
    public function getPatterns(): array
    {
        $this->compile();

        return $this->patterns;
    }

    /**
     * @return array
     */
    public function getJumps(): array
    {
        $this->compile();

        return $this->jumps;
    }
}
