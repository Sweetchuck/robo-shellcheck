<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\LintReportWrapper;

use Sweetchuck\Robo\Shellcheck\LintReportWrapper\FailureWrapper;
use Codeception\Test\Unit;
use Sweetchuck\Robo\Shellcheck\Tests\UnitActor;

/**
 * @code \Sweetchuck\Robo\Shellcheck\LintReportWrapper\FailureWrapper
 */
class FailureWrapperTest extends Unit
{

    protected UnitActor $tester;

    public function testCreate()
    {
        $data = [
            'line' => 1,
            'endLine' => 2,
            'column' => 3,
            'endColumn' => 4,
            'level' => 'error',
            'code' => '42',
            'message' => 'my-message-01',
        ];
        $failure = new FailureWrapper($data);

        $this->tester->assertSame($data['line'], $failure->line());
        $this->tester->assertSame($data['column'], $failure->column());
        $this->tester->assertSame($data['level'], $failure->severity());
        $this->tester->assertSame($data['code'], $failure->source());
        $this->tester->assertSame($data['message'], $failure->message());
    }
}
