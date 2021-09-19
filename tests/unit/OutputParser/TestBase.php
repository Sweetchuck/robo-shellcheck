<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\OutputParser;

use Codeception\Test\Unit;
use Sweetchuck\Robo\Shellcheck\OutputParserInterface;
use Sweetchuck\Robo\Shellcheck\Tests\UnitActor;

abstract class TestBase extends Unit
{
    protected UnitActor $tester;

    /**
     * @abstract
     */
    protected string $parserClass = '';

    protected OutputParserInterface $parser;

    protected function _before()
    {
        parent::_before();

        $this->parser = new $this->parserClass();
    }

    abstract public function casesParse(): array;

    /**
     * @dataProvider casesParse
     */
    public function testParse(
        array $expected,
        int $exitCode,
        string $stdOutput,
        string $stdError,
        array $options
    ) {
        $this->parser->setOptions($options);

        $this->tester->assertSame(
            $expected,
            $this->parser->parse($exitCode, $stdOutput, $stdError),
        );
    }
}
