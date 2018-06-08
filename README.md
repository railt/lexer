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

# Lexer

The lexer contains two types of runtime:
1) [`Stateless`](#stateless) - Set of algorithms for starting from scratch.
2) [`Stateful`](#stateful) - Set of algorithms for run the compiled sources.

## Stateless

### Native

Native implementation is based on the built-in php PCRE functions and faster 
than the original Hoa [more than **140 times**](https://github.com/hoaproject/Compiler/issues/81).

```php
use Railt\Lexer\NativeStateless;
use Railt\Io\File;

$lexer = new NativeStateless();
$lexer->add('T_WHITESPACE', '\s+', true);
$lexer->add('T_DIGIT', '\d+');

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
Note that this implementation is 1.5 to 3 times **slower** than the native 
PHP implementation.

```php
use Railt\Lexer\ParleStateless;
use Railt\Io\File;

$lexer = new ParleStateless();
$lexer->add('T_WHITESPACE', '\s+')->skip('T_WHITESPACE');
$lexer->add('T_DIGIT', '\d+');

foreach ($lexer->lex(File::fromSources('23 42')) as $token) {
    echo $token->name() . ' -> ' . $token->value() . ' at ' . $token->offset() . "\n";
}

// Outputs:
// T_DIGIT -> 23 at 0
// T_DIGIT -> 42 at 3
```

> Be careful: The library is not fully compatible with the PCRE regex 
syntax. See the [official documentation](http://www.benhanson.net/lexertl.html).

## Stateful

### Native

Native implementation is based on the built-in php PCRE functions.

```php
use Railt\Lexer\NativeStateful;
use Railt\Io\File;

$lexer = new NativeStateful($compiler->compile(), ['T_WHITESPACE']);
//                          ^ Compiled PCRE       ^ Skipped tokens

foreach ($lexer->lex(File::fromSources('23 42')) as $token) {
    echo $token->name() . ' -> ' . $token->value() . ' at ' . $token->offset() . "\n";
}

// Outputs:
// T_DIGIT -> 23 at 0
// T_DIGIT -> 42 at 3
```

## Compilation

```php
use Railt\Lexer\Common\PCRECompiler;

$compiler = new PCRECompiler();
$compiler->addToken('T_WHITESPACE', '\s+');
$compiler->addToken('T_DIGIT', '\d+');

echo $compiler->compile(); // 
```
