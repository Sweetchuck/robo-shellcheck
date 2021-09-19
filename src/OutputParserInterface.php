<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck;

interface OutputParserInterface
{
    public function getAssetNameMapping(): array;

    /**
     * @return $this
     */
    public function setAssetNameMapping(array $value);

    /**
     * @return $this
     */
    public function setOptions(array $options);

    public function parse(int $exitCode, string $stdOutput, string $stdError): array;
}
