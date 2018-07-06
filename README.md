<p align="center">
    <img src="https://railt.org/images/logo-dark.svg" width="200" alt="Railt" />
</p>

<p align="center">
    <a href="https://travis-ci.org/railt/lexer"><img src="https://travis-ci.org/railt/lexer.svg?branch=master" alt="Travis CI" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/lexer/?branch=master"><img src="https://scrutinizer-ci.com/g/railt/lexer/badges/coverage.png?b=master" alt="Code coverage" /></a>
    <a href="https://scrutinizer-ci.com/g/railt/lexer/?branch=master"><img src="https://scrutinizer-ci.com/g/railt/lexer/badges/quality-score.png?b=master" alt="Scrutinizer CI" /></a>
    <a href="https://packagist.org/packages/railt/lexer"><img src="https://poser.pugx.org/railt/lexer/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/railt/lexer"><img src="https://poser.pugx.org/railt/lexer/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/railt/lexer/master/LICENSE.md"><img src="https://poser.pugx.org/railt/lexer/license" alt="License MIT"></a>
</p>

> NOTE: Tests can not always pass correctly. This may be due to the inaccessibility of 
PPA servers for updating gcc and g++. The lexertl build requires the support of a modern 
compiler inside Travis CI. In this case, a gray badge will be displayed with the message "Build Error".

# Lexer

The lexer contains two types of runtime:
1) [`Basic`](#basic) - Set of algorithms with one state.
2) [`Multistate`](#multistate) - Set of algorithms with the possibility of state transition between tokens.

> In connection with the fact that there were almost no differences in 
speed between several implementations (Stateful vs Stateless) of the same algorithm, 
it was decided to abandon the immutable stateful lexers.

## API

```php
interface LexerInterface
{
    /**
     * LexerInterface constructor.
     */
    public function __construct(array $tokens = [], array $skip = []);

    /**
     * Compiling the current state of the lexer and returning stream tokens from the source file.
     *
     * @param Readable $input
     * @return \Traversable|TokenInterface[]
     */
    public function lex(Readable $input): \Traversable;

    /**
     * Add a lexer rule.
     *
     * @param string $token Token name
     * @param string $pcre Perl compatible regular expression used for token matching
     * @return LexerInterface|$this
     */
    public function add(string $token, string $pcre): LexerInterface;

    /**
     * A method for marking a token as skipped. 
     *
     * @param string $name Token name
     * @return LexerInterface
     */
    public function skip(string $name): LexerInterface;
}
```

```php
interface MultistateLexerInterface extends LexerInterface
{
    /**
     * Method for indicating the status identifier of the indicated token.
     *
     * For example, in this case, the T_STRING token will only
     * occur when the lexer is in state 1:
     *
     * <code>
     *  $lexer->add('T_QUOTE_OPEN', '"');
     *  $lexer->add('T_QUOTE_CLOSE', '"');
     *  $lexer->add('T_STRING', '[^\\"]');
     *
     *  $lexer->state('T_QUOTE_OPEN', 0, 1);
     *  $lexer->state('T_STRING', 1);
     *  $lexer->state('T_QUOTE_CLOSE', 1, 0);
     *
     *  $lexer->lex('"Hello!"');
     *  // T_QUOTE_OPEN (state 0 -> 1)
     *  // T_QUOTE_OPEN (state 1 -> 1)
     *  // T_QUOTE_CLOSE (state 1 -> 0)
     * </code>
     *
     * @param string $token Token name
     * @param int $state State identifier
     * @param int|null $nextState
     * @return MultistateLexerInterface
     */
    public function state(string $token, int $state, int $nextState = null): MultistateLexerInterface;
}
```

## Basic

### NativeRegex

`NativeRegex` implementation is based on the built-in php PCRE functions.

```php
use Railt\Lexer\Driver\NativeRegex;
use Railt\Io\File;

$lexer = new NativeRegex([
    'T_WHITESPACE'  => '\s+', 
    'T_DIGIT'       => '\d+'
], [
    'T_WHITESPACE', 
    'T_EOI'
]);

foreach ($lexer->lex(File::fromSources('23 42')) as $token) {
    echo $token->name() . ' -> ' . $token->value() . ' at ' . $token->offset() . "\n";
}

// Outputs:
// T_DIGIT -> 23 at 0
// T_DIGIT -> 42 at 3
```

### Lexertl

Experimental lexer based on the 
[C++ lexertl library](https://github.com/BenHanson/lexertl). To use it, you 
need support for [Parle extension](http://php.net/manual/en/book.parle.php).

```php
use Railt\Lexer\Driver\ParleLexer;
use Railt\Io\File;

$lexer = new ParleLexer([
    'T_WHITESPACE'  => '\s+', 
    'T_DIGIT'       => '\d+'
], [
    'T_WHITESPACE', 
    'T_EOI'
]);

foreach ($lexer->lex(File::fromSources('23 42')) as $token) {
    echo $token->name() . ' -> ' . $token->value() . ' at ' . $token->offset() . "\n";
}

// Outputs:
// T_DIGIT -> 23 at 0
// T_DIGIT -> 42 at 3
```

> Be careful: The library is not fully compatible with the PCRE regex 
syntax. See the [official documentation](http://www.benhanson.net/lexertl.html).

## Multistate

This functionality is not yet implemented.

