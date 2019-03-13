<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Runtime;

use Railt\Lexer\Exception\RegularExpressionException;

/**
 * Class Validator
 */
class Validator
{
    /**
     * @var string
     */
    public const PREG_PARSING_ERROR = 'The error occurs while compiling PCRE';

    /**
     * @var string
     */
    public const PREG_INTERNAL_ERROR = 'The given PCRE contain a syntax error';

    /**
     * @var string
     */
    public const PREG_BACKTRACK_LIMIT_ERROR = 'Backtrack limit was exhausted';

    /**
     * @var string
     */
    public const PREG_RECURSION_LIMIT_ERROR = 'Recursion limit was exhausted';

    /**
     * @var string
     */
    public const PREG_BAD_UTF8_ERROR = 'The offset didn\'t correspond to the begin of a valid UTF-8 code point';

    /**
     * @var string
     */
    public const PREG_BAD_UTF8_OFFSET_ERROR = 'Malformed UTF-8 data';

    /**
     * Checks the result for correctness.
     * <code>
     *  Validator::assert(@\preg_match(....), \preg_last_error());
     * </code>
     *
     * @param mixed|null $result PCRE function result
     * @param int $code PCRE error code
     * @return bool
     * @throws RegularExpressionException
     */
    public static function assert($result, int $code): bool
    {
        if ($code !== \PREG_NO_ERROR) {
            throw new RegularExpressionException(self::getErrorMessage($code), $code);
        }

        if ($result === null) {
            $parts = \explode(':', \error_get_last()['message'] ?? '');
            $error = \sprintf('%s, %s', self::PREG_PARSING_ERROR, \trim(\end($parts)));

            throw new RegularExpressionException($error);
        }

        return true;
    }

    /**
     * @param int $code
     * @return string
     */
    private static function getErrorMessage(int $code): string
    {
        switch ($code) {
            case \PREG_INTERNAL_ERROR:
                return self::PREG_INTERNAL_ERROR;

            case \PREG_BACKTRACK_LIMIT_ERROR:
                return self::PREG_BACKTRACK_LIMIT_ERROR;

            case \PREG_RECURSION_LIMIT_ERROR:
                return self::PREG_RECURSION_LIMIT_ERROR;

            case \PREG_BAD_UTF8_ERROR:
                return self::PREG_BAD_UTF8_ERROR;

            case \PREG_BAD_UTF8_OFFSET_ERROR:
                return self::PREG_BAD_UTF8_OFFSET_ERROR;
        }

        return 'Unexpected PCRE error (Code ' . $code . ')';
    }
}
