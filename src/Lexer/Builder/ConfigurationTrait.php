<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Builder;

use Railt\Lexer\Builder\ConfigurationInterface as Config;

/**
 * Trait ConfigurationTrait
 * @mixin Config
 */
trait ConfigurationTrait
{
    /**
     * @var int
     */
    protected $options = Config::OPTION_DOT_ALL | Config::OPTION_UNICODE | Config::OPTION_KEEP_UNKNOWN;

    /**
     * @param int $option
     * @return Config|$this
     */
    public function withOption(int $option): Config
    {
        $this->options |= $option;

        return $this;
    }

    /**
     * @param int $option
     * @return Config|$this
     */
    public function withoutOption(int $option): Config
    {
        $this->options &= ~$option;

        return $this;
    }

    /**
     * @param int $option
     * @return bool
     */
    public function hasOption(int $option): bool
    {
        return (bool)($this->options & $option);
    }
}
