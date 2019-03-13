<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Token;

use Railt\Dumper\Resolver\StringResolver;
use Railt\Dumper\TypeDumper;
use Railt\Lexer\TokenInterface;

/**
 * Class Token
 */
class Token implements TokenInterface
{
    /**
     * @var int|null
     */
    private $bytes;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var string|null
     */
    private $state;

    /**
     * Token constructor.
     *
     * @param string $name
     * @param string $value
     * @param int $offset
     * @param string|null $state
     */
    public function __construct(string $name, string $value, int $offset = 0, string $state = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->offset = $offset;
        $this->state = $state;
    }

    /**
     * @param string|null $state
     * @return Token
     */
    public function in(?string $state): self
    {
        $this->state = $state === static::DEFAULT_TOKEN_STATE ? null : $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state ?? static::DEFAULT_TOKEN_STATE;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getBytes(): int
    {
        if ($this->bytes === null) {
            $this->bytes = \strlen($this->getValue());
        }

        return $this->bytes;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $value = $this->valueToString();

        //
        // Render with non-default state
        //
        if ($this->getState() !== static::DEFAULT_TOKEN_STATE) {
            return \sprintf('%s (%s:%s)', $value, $this->getState(), $this->getName());
        }

        //
        // Otherwise
        //
        return \sprintf('%s (%s)', $value, $this->getName());
    }

    /**
     * @return string
     */
    private function valueToString(): string
    {
        $value = $this->getValue();

        return \class_exists(TypeDumper::class)
            ? (new StringResolver(TypeDumper::getInstance()))->value($value)
            : \sprintf('"%.30s"', \strlen($value) > 30 ? $value . '…' : $value);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
