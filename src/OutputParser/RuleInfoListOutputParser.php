<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\OutputParser;

class RuleInfoListOutputParser extends OutputParserBase
{

    public function parse(int $exitCode, string $stdOutput, string $stdError): array
    {
        $keyRules = $this->getExternalAssetName('rules');
        $return = [
            'exitCode' => $exitCode,
            'errorMessages' => [],
            'assets' => [
                $keyRules => [],
            ],
        ];

        if ($exitCode !== 0) {
            if ($stdError) {
                $return['errorMessages'][] = $stdError;
            }

            return $return;
        }

        $items = preg_split('/' . preg_quote(PHP_EOL . PHP_EOL) . '/u', trim($stdOutput));
        foreach ($items as $item) {
            $lines = preg_split('/\s*?\n\s*/u', $item, -1, PREG_SPLIT_NO_EMPTY);
            $rule = [];
            foreach ($lines as $line) {
                $parts = preg_split('/:\s*/u', $line, 2, PREG_SPLIT_NO_EMPTY) + [1 => ''];
                $rule[$parts[0]] = $parts[1];
            }

            $return['assets'][$keyRules][$rule['name']] = $rule;
        }

        return $return;
    }
}
