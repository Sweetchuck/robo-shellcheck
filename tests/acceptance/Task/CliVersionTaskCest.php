<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Acceptance\Task;

use Sweetchuck\Robo\Shellcheck\Tests\AcceptanceActor;
use Sweetchuck\Robo\Shellcheck\Tests\Helper\RoboFiles\ShellcheckRoboFile;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\Task\CliVersionTask<extended>
 * @covers \Sweetchuck\Robo\Shellcheck\ShellcheckTaskLoader
 */
class CliVersionTaskCest
{
    public function runVersionSuccess(AcceptanceActor $I): void
    {
        $id = 'shellcheck:version';
        $I->runRoboTask($id, ShellcheckRoboFile::class, 'shellcheck:version');

        $expectedExitCode = 0;
        $expectedStdOutput = implode(PHP_EOL, [
            "shellcheck.version.description: 'ShellCheck - shell script analysis tool'",
            "shellcheck.version.version: 9.9.9",
            "shellcheck.version.license: 'GNU General Public License, version 3'",
            "shellcheck.version.website: 'https://www.shellcheck.net'",
            '',
        ]);
        $expectedStdError = implode(PHP_EOL, [
            ' [ShellCheck - Version] runs "shellcheck --version"',
            '',
        ]);

        $I->assertSame($expectedExitCode, $I->getRoboTaskExitCode($id));
        $I->assertSame($expectedStdOutput, $I->getRoboTaskStdOutput($id));
        $I->assertSame($expectedStdError, $I->getRoboTaskStdError($id));
    }
}
