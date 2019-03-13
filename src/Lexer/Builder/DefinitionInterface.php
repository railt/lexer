<?php
/**
 * This file is part of Builder package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Builder;

use Railt\Lexer\TokenInterface;

/**
 * Interface DefinitionInterface
 */
interface DefinitionInterface
{
    /**
     * @var string
     */
    public const DEFAULT_STATE = TokenInterface::DEFAULT_TOKEN_STATE;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getPattern(): string;

    /**
     * @param string|null $state
     * @return DefinitionInterface
     */
    public function in(?string $state): self;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @param string|null $state
     * @return DefinitionInterface
     */
    public function then(?string $state): self;

    /**
     * @return string
     */
    public function getNextState(): string;

    /**
     * @return bool
     */
    public function isStateWillChanged(): bool;
}
