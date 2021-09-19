<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\OutputParser;

class VersionOutputParser extends OutputParserBase
{

    public function parse(int $exitCode, string $stdOutput, string $stdError): array
    {
        $return = [
            'exitCode' => $exitCode,
            'errorMessages' => [],
            'assets' => [],
        ];

        if ($exitCode !== 0) {
            if ($stdError) {
                $return['errorMessages'][] = $stdError;
            }

            return $return;
        }

        $stdOutputLines = preg_split('/\s*?[\n\r]+/', $stdOutput, -1, PREG_SPLIT_NO_EMPTY);

        $key = $this->getExternalAssetName('description');
        $return['assets'][$key] = array_shift($stdOutputLines);

        foreach ($stdOutputLines as $stdOutputLine) {
            $parts = explode(': ', $stdOutputLine, 2) + [1 => ''];
            $key = $this->getExternalAssetName($parts[0]);
            $return['assets'][$key] = $parts[1];
        }

        return $return;
    }
}
