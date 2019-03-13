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
 * Interface ConfigurationInterface
 */
interface ConfigurationInterface
{
    /**
     * @var int
     */
    public const OPTION_CASE_INSENSITIVE = 0x01;

    /**
     * @var int
     */
    public const OPTION_MULTILINE = 0x02;

    /**
     * @var int
     */
    public const OPTION_DOT_ALL = 0x04;

    /**
     * @var int
     */
    public const OPTION_UNICODE = 0x08;

    /**
     * @var int
     */
    public const OPTION_KEEP_UNKNOWN = 0x10;

    /**
     * @param int $option
     * @return ConfigurationInterface|$this
     */
    public function withOption(int $option): self;

    /**
     * @param int $option
     * @return ConfigurationInterface
     */
    public function withoutOption(int $option): self;

    /**
     * @param int $option
     * @return bool
     */
    public function hasOption(int $option): bool;
}
