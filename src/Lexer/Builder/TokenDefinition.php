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
 * Class TokenDefinition
 */
class TokenDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $state = self::DEFAULT_STATE;

    /**
     * @var string|null
     */
    private $next;

    /**
     * TokenDefinition constructor.
     *
     * @param string $name
     * @param string $pattern
     */
    public function __construct(string $name, string $pattern)
    {
        $this->name    = $name;
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TokenDefinition|$this
     */
    public function rename(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @return TokenDefinition|$this
     */
    public function redefine(string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * @param string|null $state
     * @return DefinitionInterface|$this
     */
    public function in(?string $state): DefinitionInterface
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @param string|null $state
     * @return DefinitionInterface|$this
     */
    public function then(?string $state): DefinitionInterface
    {
        $this->next = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getNextState(): string
    {
        return $this->next ?? $this->getState();
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state ?? self::DEFAULT_STATE;
    }

    /**
     * @return bool
     */
    public function isStateWillChanged(): bool
    {
        return $this->getState() !== $this->getNextState();
    }

    /**
     * @return TokenDefinition|DefinitionInterface|$this
     */
    public function thenDefault(): self
    {
        return $this->then(static::DEFAULT_STATE);
    }
}
