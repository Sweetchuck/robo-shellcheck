<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Acceptance\Task;

use Sweetchuck\Robo\Shellcheck\Tests\AcceptanceActor;
use Sweetchuck\Robo\Shellcheck\Tests\Helper\RoboFiles\ShellcheckRoboFile;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\Task\CliCheckInputTask<extended>
 * @covers \Sweetchuck\Robo\Shellcheck\ShellcheckTaskLoader
 */
class CliCheckInputTaskCest
{
    public function runCheckFiles(AcceptanceActor $I): void
    {
        $fixturesDir ='tests/_data/fixtures';
        $files = [
            "$fixturesDir/sample-01.bash" => [
                'fileName' => "$fixturesDir/sample-01.bash",
                'content' => file_get_contents("$fixturesDir/sample-01.bash"),
            ],
        ];

        $id = 'shellcheck:check:input';
        $I->runRoboTask(
            $id,
            ShellcheckRoboFile::class,
            'shellcheck:check:input',
            json_encode($files),
        );

        $expectedExitCode = 1;
        $expectedStdOutput = implode(PHP_EOL, [
            'tests/_data/fixtures/sample-01.bash',
            '+----------+------+----------------------------------------------------------------+',
            '| Severity | Line | Message                                                        |',
            '+----------+------+----------------------------------------------------------------+',
            '| warning  |    6 | bar appears unused. Verify use (or export if used externally). |',
            '| error    |    9 | Double quote array expansions to avoid re-splitting elements.  |',
            '+----------+------+----------------------------------------------------------------+',
            '',
        ]);

        $expectedStdErrors = [
            ' [ShellCheck - Check stdInput] runs "shellcheck --format=\'json\' \'-\'"',
            <<< 'BASH'
[debug] echo -n '#!/usr/bin/env bash


# Severity: warning.
foo='\''value'\''
bar=$foo

# Severity: error.
echo $@
' | shellcheck --format='json' --shell='bash' '-'

BASH
,
            ' [Sweetchuck\Robo\Shellcheck\Task\CliCheckInputTask]',
            ' [Sweetchuck\Robo\Shellcheck\Task\CliCheckInputTask]  Exit code 1',
            '',
        ];

        $I->assertSame($expectedExitCode, $I->getRoboTaskExitCode($id));
        $I->assertSame($expectedStdOutput, $I->getRoboTaskStdOutput($id));
        $actualStdError = $I->getRoboTaskStdError($id);
        foreach ($expectedStdErrors as $expectedStdError) {
            $I->assertStringContainsString($expectedStdError, $actualStdError);
        }
    }
}
