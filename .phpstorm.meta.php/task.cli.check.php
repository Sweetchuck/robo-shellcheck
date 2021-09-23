<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META
{
    registerArgumentsSet(
        'robo_shellcheck_color',
        'auto',
        'always',
        'never',
        '',
    );

    registerArgumentsSet(
        'robo_shellcheck_severity',
        'error',
        'warning',
        'info',
        'style',
        '',
    );

    registerArgumentsSet(
        'robo_shellcheck_format',
        'checkstyle',
        'diff',
        'gcc',
        'json',
        'json1',
        'quiet',
        'tty',
        '',
    );

    registerArgumentsSet(
        'robo_shellcheck_shell',
        'sh',
        'bash',
        'dash',
        'ksh',
        '',
    );

    expectedReturnValues(
        \Sweetchuck\Robo\Shellcheck\Task\CliCheckTask::getColor(),
        argumentsSet('robo_shellcheck_color'),
    );

    expectedArguments(
        \Sweetchuck\Robo\Shellcheck\Task\CliCheckTask::setColor(),
        0,
        argumentsSet('robo_shellcheck_color'),
    );

    expectedReturnValues(
        \Sweetchuck\Robo\Shellcheck\Task\CliCheckTask::getSeverity(),
        argumentsSet('robo_shellcheck_severity'),
    );

    expectedArguments(
        \Sweetchuck\Robo\Shellcheck\Task\CliCheckTask::setSeverity(),
        0,
        argumentsSet('robo_shellcheck_severity'),
    );

    expectedReturnValues(
        \Sweetchuck\Robo\Shellcheck\Task\CliCheckTask::getFormat(),
        argumentsSet('robo_shellcheck_format'),
    );

    expectedArguments(
        \Sweetchuck\Robo\Shellcheck\Task\CliCheckTask::setFormat(),
        0,
        argumentsSet('robo_shellcheck_format'),
    );

    expectedReturnValues(
        \Sweetchuck\Robo\Shellcheck\Task\CliCheckTask::getShell(),
        argumentsSet('robo_shellcheck_shell'),
    );

    expectedArguments(
        \Sweetchuck\Robo\Shellcheck\Task\CliCheckTask::setShell(),
        0,
        argumentsSet('robo_shellcheck_shell'),
    );
}
