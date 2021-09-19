<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\OutputParser;

use Sweetchuck\Robo\Shellcheck\OutputParser\VersionOutputParser;
use Symfony\Component\Yaml\Yaml;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\OutputParser\VersionOutputParser<extended>
 */
class VersionOutputParserTest extends TestBase
{
    protected string $parserClass = VersionOutputParser::class;

    public function casesParse(): array
    {
        $fileName = codecept_data_dir('testCases/OutputParser/Version.yml');
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
                    'description' => 'shellcheck.version.description',
                    'version' => 'shellcheck.version.version',
                    'license' => 'shellcheck.version.license',
                    'website' => 'shellcheck.version.website',
                ],
            ],
        ];

        foreach ($cases as $key => $case) {
            $cases[$key] = array_replace_recursive($template, $case);
        }

        return $cases;
    }
}
