<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\OutputParser;

use Sweetchuck\Robo\Shellcheck\OutputParser\RuleInfoListOutputParser;
use Symfony\Component\Yaml\Yaml;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\OutputParser\RuleInfoListOutputParser<extended>
 */
class RuleInfoListOutputParserTest extends TestBase
{

    protected string $parserClass = RuleInfoListOutputParser::class;

    public function casesParse(): array
    {
        $fileName = codecept_data_dir('testCases/OutputParser/RuleInfoList.yml');
        $cases = Yaml::parseFile($fileName);
        $template = [
            'expected' => [
                'exitCode' => 0,
                'errorMessages' => [],
            ],
            'exitCode' => 0,
            'stdOutput' => '',
            'stdError' => '',
            'options' => [
                'assetNameMapping' => [
                    'rules' => 'shellcheck.optional.rules',
                ],
            ],
        ];

        foreach ($cases as $key => $case) {
            $cases[$key] = array_replace_recursive($template, $case);
        }

        return $cases;
    }
}
