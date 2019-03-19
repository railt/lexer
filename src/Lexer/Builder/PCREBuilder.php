<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Builder;

use Railt\Lexer\Exception\RegularExpressionException;
use Railt\Lexer\LexerInterface;
use Railt\Lexer\Runtime\Validator;

/**
 * Class PCREBuilder
 */
class PCREBuilder
{
    /**
     * @var array|string[]
     */
    public const PREPENDED_TOKENS = [];

    /**
     * @var string
     */
    private const REGEX_PATTERN = '%s\G(?|%s)%1$s%s';

    /**
     * @var string
     */
    private const REGEX_CHUNK_PATTERN = '(?:%2$s)(*MARK:%1$s)';

    /**
     * @var string
     */
    private const REGEX_CHUNK_DELIMITER = '|';

    /**
     * Regex delimiter
     *
     * @var string
     */
    private const REGEX_DELIMITER = '/';

    /**
     * @var string
     */
    private const FLAG_UNICODE = 'u';

    /**
     * @var string
     */
    private const FLAG_DOT_ALL = 's';

    /**
     * @var string
     */
    private const FLAG_CASE_INSENSITIVE = 'i';

    /**
     * @var string
     */
    private const FLAG_ANALYZED = 'S';

    /**
     * @var string
     */
    private const FLAG_MULTILINE = 'm';

    /**
     * @var string[]
     */
    private const FLAGS_MAPPING = [
        ConfigurationInterface::OPTION_DOT_ALL          => self::FLAG_DOT_ALL,
        ConfigurationInterface::OPTION_UNICODE          => self::FLAG_UNICODE,
        ConfigurationInterface::OPTION_MULTILINE        => self::FLAG_MULTILINE,
        ConfigurationInterface::OPTION_CASE_INSENSITIVE => self::FLAG_CASE_INSENSITIVE,
    ];

    /**
     * @var string[]
     */
    private const FLAGS_DEFAULT = [
        self::FLAG_ANALYZED,
    ];

    /**
     * @var array|string[]
     */
    private $tokens;

    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * Compiler constructor.
     *
     * @param ConfigurationInterface $config
     * @param array $tokens
     */
    public function __construct(ConfigurationInterface $config, array $tokens = [])
    {
        $this->tokens = $tokens;
        $this->config = $config;
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfig(): ConfigurationInterface
    {
        return $this->config;
    }

    /**
     * @param string $token
     * @param string $pcre
     * @return PCREBuilder
     */
    public function add(string $token, string $pcre): self
    {
        $this->tokens[$token] = $pcre;

        return $this;
    }

    /**
     * @return string
     * @throws RegularExpressionException
     */
    public function compile(): string
    {
        $tokens = $this->tokens;

        if ($this->config->hasOption(ConfigurationInterface::OPTION_KEEP_UNKNOWN)) {
            $tokens[LexerInterface::T_UNKNOWN] = '.+?';
        }

        return $this->render($this->renderBody($tokens));
    }

    /**
     * @param string $body
     * @return string
     */
    private function render(string $body): string
    {
        return \sprintf(self::REGEX_PATTERN, self::REGEX_DELIMITER, $body, $this->flags());
    }

    /**
     * @return string
     */
    private function flags(): string
    {
        $result = \implode('', self::FLAGS_DEFAULT);

        foreach (self::FLAGS_MAPPING as $option => $flag) {
            if ($this->config->hasOption($option)) {
                $result .= $flag;
            }
        }

        return $result;
    }

    /**
     * @param array $tokens
     * @return string
     * @throws RegularExpressionException
     */
    private function renderBody(array $tokens): string
    {
        $chunks = [];

        foreach ($tokens as $name => $pattern) {
            $pcre = $this->escapePattern($pattern);

            try {
                Validator::assert(@\preg_match($this->render($pcre), ''), \preg_last_error());
            } catch (RegularExpressionException $e) {
                $error = \sprintf('Token %s contains a syntax error in the PCRE /%s/', $name, $pcre);
                throw new RegularExpressionException($error);
            }

            $chunks[] = \vsprintf(self::REGEX_CHUNK_PATTERN, [
                $this->escapeName($name),
                $this->escapePattern($pattern),
            ]);
        }

        return \implode(self::REGEX_CHUNK_DELIMITER, $chunks);
    }

    /**
     * @param string $pattern
     * @return string
     */
    protected function escapePattern(string $pattern): string
    {
        return \addcslashes($pattern, static::REGEX_DELIMITER);
    }

    /**
     * @param string $token
     * @return string
     */
    protected function escapeName(string $token): string
    {
        return \preg_quote($token, static::REGEX_DELIMITER);
    }
}
