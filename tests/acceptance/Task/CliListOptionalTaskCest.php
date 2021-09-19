<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Acceptance\Task;

use Sweetchuck\Robo\Shellcheck\Tests\AcceptanceActor;
use Sweetchuck\Robo\Shellcheck\Tests\Helper\RoboFiles\ShellcheckRoboFile;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\Task\CliListOptionalTask<extended>
 * @covers \Sweetchuck\Robo\Shellcheck\ShellcheckTaskLoader
 */
class CliListOptionalTaskCest
{
    public function runVersionSuccess(AcceptanceActor $I): void
    {
        $id = 'shellcheck:list-optional';
        $I->runRoboTask($id, ShellcheckRoboFile::class, 'shellcheck:list-optional');

        $expectedExitCode = 0;
        $expectedStdOutput = <<< 'YAML'
shellcheck.optional.rules:
    add-default-case:
        name: add-default-case
        desc: 'Suggest adding a default case in `case` statements'
        example: 'case $? in 0) echo ''Success'';; esac'
        fix: 'case $? in 0) echo ''Success'';; *) echo ''Fail'' ;; esac'
    avoid-nullary-conditions:
        name: avoid-nullary-conditions
        desc: 'Suggest explicitly using -n in `[ $var ]`'
        example: '[ "$var" ]'
        fix: '[ -n "$var" ]'

YAML;

        $expectedStdError = implode(PHP_EOL, [
            ' [ShellCheck - List optional rules] runs "shellcheck --list-optional"',
            '',
        ]);

        $I->assertSame($expectedExitCode, $I->getRoboTaskExitCode($id));
        $I->assertStringStartsWith($expectedStdOutput, $I->getRoboTaskStdOutput($id));
        $I->assertSame($expectedStdError, $I->getRoboTaskStdError($id));
    }
}
