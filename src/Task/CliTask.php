<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Task;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Common\OutputAwareTrait;
use Robo\Contract\CommandInterface;
use Consolidation\AnnotatedCommand\Output\OutputAwareInterface;
use Robo\Result;
use Stringy\StaticStringy;
use Sweetchuck\Robo\Shellcheck\OutputParserInterface;
use Sweetchuck\Utils\Filter\ArrayFilterEnabled;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Process\Process;

abstract class CliTask extends BaseTask implements
    CommandInterface,
    ContainerAwareInterface,
    OutputAwareInterface
{
    use ContainerAwareTrait;
    use OutputAwareTrait;

    protected string $command = '';

    protected string $processStdOutput = '';

    protected string $processStdError = '';

    protected int $processExitCode = 0;

    protected string $outputParserClass = '';

    protected ?OutputParserInterface $outputParser = null;

    protected array $outputParserAssetNameMapping = [];

    // region Option

    // region envVars

    protected array $envVars = [];

    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    public function getEnvVar(string $name): ?string
    {
        return $this->envVars[$name] ?? null;
    }

    /**
     * @return $this
     */
    public function setEnvVars(array $envVars)
    {
        $this->envVars = $envVars;

        return $this;
    }

    /**
     * @return $this
     */
    public function setEnvVar(string $name, $value)
    {
        $this->envVars[$name] = (string) $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function delEnvVar(string $name)
    {
        unset($this->envVars[$name]);

        return $this;
    }
    // endregion

    // region executable
    /**
     * @var string
     */
    protected string $executable = 'shellcheck';

    public function getExecutable(): string
    {
        return $this->executable;
    }

    /**
     * @return $this
     */
    public function setExecutable(string $value)
    {
        $this->executable = $value;

        return $this;
    }
    // endregion

    // region color
    /**
     * @var string
     */
    protected $color = '';

    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return $this
     */
    public function setColor(string $color)
    {
        $this->color = $color;

        return $this;
    }
    // endregion

    // endregion

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (array_key_exists('envVars', $options)) {
            $this->setEnvVars($options['envVars']);
        }

        if (array_key_exists('executable', $options)) {
            $this->setExecutable($options['executable']);
        }

        if (array_key_exists('color', $options)) {
            $this->setColor($options['color']);
        }

        return $this;
    }

    protected function getCommandOptions(): array
    {
        $options = parent::getCommandOptions();

        $options['executable'] = [
            'type' => 'other',
            'value' => $this->getExecutable(),
        ];

        $options['color'] = [
            'type' => 'option:value',
            'value' => $this->getColor(),
        ];

        foreach ($this->getEnvVars() as $name => $value) {
            $options["envVar:$name"] = [
                'type' => 'environment',
                'name' => $name,
                'value' => $value,
            ];
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        $commandOptions = $this->getCommandOptions();
        $enabledFilter = new ArrayFilterEnabled();

        $envPattern = [];
        $envArgs = [];

        $cmdPattern = [];
        $cmdArgs = [];

        $cmdAsIs = [];

        $cmdPattern[] = '%s';
        $cmdArgs[] = escapeshellcmd($commandOptions['executable']['value']);

        foreach ($commandOptions as $optionName => $option) {
            $optionNameCli = $option['name'] ?? StaticStringy::dasherize($optionName);
            switch ($option['type']) {
                case 'environment':
                    if ($option['value'] !== null) {
                        $envVarName = $option['name'] ?? $optionName;
                        $envPattern[] = "$envVarName=%s";
                        $envArgs[] = escapeshellarg($option['value']);
                    }
                    break;

                case 'arg:value':
                case 'option:value':
                    if ($option['value']) {
                        $cmdPattern[] = $option['type'] === 'option:value' ? "--$optionNameCli=%s" : '%s';
                        $cmdArgs[] = escapeshellarg((string) $option['value']);
                    }
                    break;

                case 'option:flag':
                    if ($option['value']) {
                        $cmdPattern[] = "--$optionNameCli";
                    }
                    break;

                case 'option:list':
                    $items = array_filter($option['value'], $enabledFilter);
                    if ($items) {
                        $cmdPattern[] = $option['type'] === 'option:list' ? "--$optionNameCli=%s" : '%s';
                        $cmdArgs[] = escapeshellarg(implode($option['separator'] ?? ',', array_keys($items)));
                    }
                    break;

                case 'arg:values':
                    foreach ($option['value'] as $item) {
                        $cmdAsIs[] = escapeshellarg($item);
                    }
                    break;
            }
        }

        $wd = $this->getWorkingDirectory();

        $chDir = $wd ? sprintf('cd %s &&', escapeshellarg($wd)) : '';
        $env = vsprintf(implode(' ', $envPattern), $envArgs);
        $cmd = vsprintf(implode(' ', $cmdPattern), $cmdArgs);
        $asIs = implode(' ', $cmdAsIs);

        return implode(' ', array_filter([$chDir, $env, $cmd, $asIs]));
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this
            ->runInit()
            ->runHeader()
            ->runDoIt()
            ->runInitAssets()
            ->runProcessOutputs()
            ->runReturn();
    }

    protected function runInit()
    {
        $this->command = $this->getCommand();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function runHeader()
    {
        $this->printTaskInfo(
            'runs "<info>{command}</info>"',
            [
                'command' => $this->command,
            ]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function runDoIt()
    {
        $process = $this
            ->getProcessHelper()
            ->run(
                $this->output(),
                [
                    'bash',
                    '-c',
                    $this->command,
                ],
                null,
                $this->getProcessRunCallbackWrapper()
            );

        $this->processExitCode = $process->getExitCode();
        $this->processStdOutput = $process->getOutput();
        $this->processStdError = $process->getErrorOutput();

        return $this;
    }

    /**
     * @return $this
     */
    protected function runInitAssets()
    {
        $this->assets = [];

        return $this;
    }

    /**
     * @return $this
     */
    protected function runProcessOutputs()
    {
        $outputParser = $this->getOutputParser();
        if (!$outputParser) {
            return $this;
        }

        // @todo ExitCode control.
        $result = $outputParser->parse($this->processExitCode, $this->processStdOutput, $this->processStdError);
        if (isset($result['assets'])) {
            $this->assets = $result['assets'];
        }

        return $this;
    }

    protected function runReturn(): Result
    {
        return new Result(
            $this,
            $this->getTaskResultCode(),
            $this->getTaskResultMessage(),
            $this->getAssetsWithPrefixedNames()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function runPrepareAssets()
    {
        return $this;
    }

    protected function getTaskResultCode(): int
    {
        return $this->processExitCode;
    }

    protected function getTaskResultMessage(): string
    {
        return $this->processStdError;
    }

    protected function getOutputParser(): ?OutputParserInterface
    {
        if (!$this->outputParser && $this->outputParserClass) {
            $this->outputParser = new $this->outputParserClass();
            $this->outputParser->setAssetNameMapping($this->outputParserAssetNameMapping);
        }

        return $this->outputParser;
    }

    protected function getProcessRunCallbackWrapper(): \Closure
    {
        return function (string $type, string $data): void {
            $this->processRunCallback($type, $data);
        };
    }

    protected function processRunCallback(string $type, string $data): void
    {
        switch ($type) {
            case Process::OUT:
                $this->output()->write($data);
                break;

            case Process::ERR:
                $this->printTaskError($data);
                break;
        }
    }

    protected function getProcessHelper(): ProcessHelper
    {
        // @todo Check that everything is available.
        return  $this
            ->getContainer()
            ->get('application')
            ->getHelperSet()
            ->get('process');
    }
}
