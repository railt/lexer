<?php

namespace PHPSTORM_META {

    registerArgumentsSet('railt_lexer_configuration_options',
        \Railt\Lexer\Builder\ConfigurationInterface::OPTION_CASE_INSENSITIVE |
        \Railt\Lexer\Builder\ConfigurationInterface::OPTION_MULTILINE |
        \Railt\Lexer\Builder\ConfigurationInterface::OPTION_DOT_ALL |
        \Railt\Lexer\Builder\ConfigurationInterface::OPTION_UNICODE |
        \Railt\Lexer\Builder\ConfigurationInterface::OPTION_KEEP_UNKNOWN
    );

    expectedArguments(\Railt\Lexer\Builder\ConfigurationInterface::withOption(), 0, argumentsSet('railt_lexer_configuration_options'));
    expectedArguments(\Railt\Lexer\Builder\ConfigurationInterface::withoutOption(), 0, argumentsSet('railt_lexer_configuration_options'));
    expectedArguments(\Railt\Lexer\Builder\ConfigurationInterface::hasOption(), 0, argumentsSet('railt_lexer_configuration_options'));

    expectedArguments(\Railt\Lexer\Builder::__construct(), 0, argumentsSet('railt_lexer_configuration_options'));
}
