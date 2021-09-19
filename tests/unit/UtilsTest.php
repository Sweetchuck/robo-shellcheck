<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit;

use Codeception\Test\Unit;
use Sweetchuck\Robo\Shellcheck\Tests\UnitActor;
use Sweetchuck\Robo\Shellcheck\Utils;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\Utils
 */
class UtilsTest extends Unit
{
    protected UnitActor $tester;

    public function casesDetectShellDialog(): array
    {
        return [
            'empty' => [
                null,
                [],
            ],
            'from fileName a.bash' => [
                'bash',
                [
                    'fileName' => 'a.bash',
                ],
            ],
            'from fileName /a/b.bash' => [
                'bash',
                [
                    'fileName' => '/a/b.bash',
                ],
            ],
            'from fileName .bash' => [
                null,
                [
                    'fileName' => '.bash',
                ],
            ],
            'from fileName /a/.bash' => [
                null,
                [
                    'fileName' => '/a/.bash',
                ],
            ],
            'from fileName /a/foo' => [
                null,
                [
                    'fileName' => '/a/foo',
                ],
            ],
            'from fileName /a/bash' => [
                null,
                [
                    'fileName' => '/a/bash',
                ],
            ],
            'from content #!bash' => [
                'bash',
                [
                    'content' => "#!bash\nfoo",
                ],
            ],
            'from content #!/bin/bash -x' => [
                'bash',
                [
                    'content' => "#!/bin/bash -x\nfoo",
                ],
            ],
            'from content #!/bin/bash' => [
                'bash',
                [
                    'content' => "#!/bin/bash\nfoo",
                ],
            ],
            'from content #!/usr/bin/env bash' => [
                'bash',
                [
                    'content' => "#!/usr/bin/env bash\nfoo",
                ],
            ],
            'from content #!/usr/bin/env bash -x' => [
                'bash',
                [
                    'content' => "#!/usr/bin/env bash -x\nfoo",
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesDetectShellDialog
     */
    public function testDetectShellDialog(?string $expected, array $file)
    {
        $this->tester->assertSame($expected, Utils::detectShellDialog($file));
    }
}
