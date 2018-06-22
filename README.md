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
1) [`Stateless`](#stateless) - Set of algorithms for starting from scratch.
2) [`Stateful`](#stateful) - Set of algorithms for run the compiled sources.

## Stateless

### Native

Native implementation is based on the built-in php PCRE functions and faster 
than the original Hoa [more than **140 times**](https://github.com/hoaproject/Compiler/issues/81).

```php
use Railt\Lexer\Driver\NativeStateless;
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

```php
use Railt\Lexer\Driver\ParleStateless;
use Railt\Io\File;

$lexer = new ParleStateless();
$lexer->add('T_WHITESPACE', '\s+', true);
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
use Railt\Lexer\Driver\NativeStateful;
use Railt\Io\File;

$lexer = new NativeStateful($pcre->compile(), ['T_WHITESPACE']);
//                          ^ Compiled PCRE   ^ Skipped tokens

foreach ($lexer->lex(File::fromSources('23 42')) as $token) {
    echo $token->name() . ' -> ' . $token->value() . ' at ' . $token->offset() . "\n";
}

// Outputs:
// T_DIGIT -> 23 at 0
// T_DIGIT -> 42 at 3
```

## Compilation to PCRE

```php
use Railt\Lexer\Driver\Common\PCRECompiler;

$compiler = new PCRECompiler();
$compiler->add('T_WHITESPACE', '\s+');
$compiler->add('T_DIGIT', '\d+');

echo $compiler->compile(); // "/\G(?P<T_WHITESPACE>\s+)|(?P<T_DIGIT>\d+)|(?P<T_UNKNOWN>.*?)/usS"
```
