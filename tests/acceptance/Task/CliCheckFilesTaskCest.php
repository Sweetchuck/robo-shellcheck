<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Acceptance\Task;

use Sweetchuck\Robo\Shellcheck\Tests\AcceptanceActor;
use Sweetchuck\Robo\Shellcheck\Tests\Helper\RoboFiles\ShellcheckRoboFile;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\Task\CliCheckFilesTask<extended>
 * @covers \Sweetchuck\Robo\Shellcheck\ShellcheckTaskLoader
 */
class CliCheckFilesTaskCest
{
    public function runCheckFiles(AcceptanceActor $I): void
    {
        $fixturesDir ='tests/_data/fixtures';
        $id = 'shellcheck:check:files';
        $I->runRoboTask(
            $id,
            ShellcheckRoboFile::class,
            'shellcheck:check:files',
            "$fixturesDir/sample-01.bash",
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
            ' [ShellCheck - Check files] runs "shellcheck --format=\'json\' \'tests/_data/fixtures/sample-01.bash\'"',
            ' [Sweetchuck\Robo\Shellcheck\Task\CliCheckFilesTask]',
            ' [Sweetchuck\Robo\Shellcheck\Task\CliCheckFilesTask]  Exit code 1',
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
