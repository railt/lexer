<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer;

use Railt\Lexer\Builder\ConfigurationInterface;
use Railt\Lexer\Builder\DefinitionInterface;
use Railt\Lexer\Builder\ProvidesLexer;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface extends ConfigurationInterface, ProvidesLexer
{
    /**
     * @param string $token
     * @param string $pattern
     * @param string|null $state
     * @param string|null $then
     * @return DefinitionInterface
     */
    public function add(string $token, string $pattern, string $state = null, string $then = null): DefinitionInterface;

    /**
     * @param string $token
     * @return BuilderInterface|$this
     */
    public function skip(string $token): self;

    /**
     * @return iterable|DefinitionInterface[]
     */
    public function all(): iterable;

    /**
     * @param string $initialState
     * @return LexerInterface
     */
    public function build(string $initialState = DefinitionInterface::DEFAULT_STATE): LexerInterface;

    /**
     * @return array|string[]
     */
    public function getPatterns(): array;

    /**
     * @return array|array[]
     */
    public function getJumps(): array;

    /**
     * @return array|string[]
     */
    public function getSkippedTokens(): array;
}
