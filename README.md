<p align="center">
    <img src="https://railt.org/images/logo-dark.svg" width="200" alt="Railt" />
</p>
<p align="center">
    <a href="https://travis-ci.org/railt/lexer"><img src="https://travis-ci.org/railt/lexer.svg?branch=1.4.x" alt="Travis CI" /></a>
    <a href="https://codeclimate.com/github/railt/lexer/test_coverage"><img src="https://api.codeclimate.com/v1/badges/8f4b0e28928bf2b445b2/test_coverage" /></a>
    <a href="https://codeclimate.com/github/railt/lexer/maintainability"><img src="https://api.codeclimate.com/v1/badges/8f4b0e28928bf2b445b2/maintainability" /></a>
</p>
<p align="center">
    <a href="https://packagist.org/packages/railt/lexer"><img src="https://img.shields.io/badge/PHP-7.1+-6f4ca5.svg" alt="PHP 7.1+"></a>
    <a href="https://railt.org"><img src="https://img.shields.io/badge/official-site-6f4ca5.svg" alt="railt.org"></a>
    <a href="https://discord.gg/ND7SpD4"><img src="https://img.shields.io/badge/discord-chat-6f4ca5.svg" alt="Discord"></a>
    <a href="https://packagist.org/packages/railt/lexer"><img src="https://poser.pugx.org/railt/lexer/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/railt/lexer"><img src="https://poser.pugx.org/railt/lexer/downloads" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/railt/lexer/1.4.x/LICENSE.md"><img src="https://poser.pugx.org/railt/lexer/license" alt="License MIT"></a>
</p>

# Lexer

> Note: All questions and issues please send 
to [https://github.com/railt/railt/issues](https://github.com/railt/railt/issues)

## Builder

### Configuration

```php
<?php
use Railt\Lexer\Builder;

$builder = new Builder();

$builder->withOption(Builder::OPTION_CASE_INSENSITIVE);
$builder->withoutOption(Builder::OPTION_UNICODE);
``` 

**Options:**

- `Builder::OPTION_CASE_INSENSITIVE` - Same with `i` modifier of [preg_xxx](http://php.net/manual/en/reference.pcre.pattern.modifiers.php) PHP functions.
- `Builder::OPTION_MULTILINE` - Same with `m` modifier of [preg_xxx](http://php.net/manual/en/reference.pcre.pattern.modifiers.php) PHP functions.
- `Builder::OPTION_DOT_ALL` - Same with `s` modifier of [preg_xxx](http://php.net/manual/en/reference.pcre.pattern.modifiers.php) PHP functions.
- `Builder::OPTION_UNICODE` - Same with `u` modifier of [preg_xxx](http://php.net/manual/en/reference.pcre.pattern.modifiers.php) PHP functions.
- `Builder::OPTION_KEEP_UNKNOWN` - Adds capture of undeclared tokens in the output.

### Tokens Definition

```php
<?php
use Railt\Lexer\Builder;

$builder = new Builder();

// $builder->add(string $token, string $pattern)

$builder->add('T_DIGIT', '\d+');
$builder->add('T_CONST', '\w+');
$builder->add('T_WHITESPACE', '\s+');
```

### Multistate Tokens Definition

```php
<?php
use Railt\Lexer\Builder;

$builder = new Builder();

// $builder->add(string $token, string $pattern, string $currentState, string $nextState)

$builder->add('T_COMMENT_OPEN', '/\*\*', null, 'comment');
$builder->add('T_COMMENT', '(?:(?!\*/).)+', 'comment');
$builder->add('T_COMMENT_CLOSE', '\*/', 'comment', 'default');

// Alter syntax
// $builder->add('example', 'pattern')->in('state')->then('next state');
```

### Creating a Lexer

```php
<?php
use Railt\Lexer\Builder;

$builder = new Builder();

// .... configuration

// List of PCRE patterns
$patterns = $builder->getPatterns();

// List of token transitions
$transitions = $builder->getJumps();
```

### Lexer Runtime

```php
<?php
use Railt\Io\File;
use Railt\Lexer\Builder;

$builder = new Builder();

// .... configuration

/** @var \Railt\Lexer\LexerInterface $lexer */
$lexer = $builder->build();

$iterator = $lexer->lex(File::fromPathname(__DIR__ . '/example.txt'));

foreach ($iterator as $token) {
    echo $token . "\n";
}
```
