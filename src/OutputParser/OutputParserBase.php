<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\OutputParser;

use Sweetchuck\Robo\Shellcheck\OutputParserInterface;

abstract class OutputParserBase implements OutputParserInterface
{

    // region assetNameMapping
    protected array $assetNameMapping = [];

    public function getAssetNameMapping(): array
    {
        return $this->assetNameMapping;
    }

    /**
     * @return $this
     */
    public function setAssetNameMapping(array $value)
    {
        $this->assetNameMapping = $value;

        return $this;
    }
    // endregion

    public function setOptions(array $options)
    {
        if (array_key_exists('assetNameMapping', $options)) {
            $this->setAssetNameMapping($options['assetNameMapping']);
        }

        return $this;
    }

    protected function getExternalAssetName(string $internalAssetName): string
    {
        $assetNameMapping = $this->getAssetNameMapping();

        return $assetNameMapping[$internalAssetName];
    }

    abstract public function parse(int $exitCode, string $stdOutput, string $stdError): array;
}
