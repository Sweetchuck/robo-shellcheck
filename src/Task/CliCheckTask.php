<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Task;

use Sweetchuck\LintReport\ReporterInterface;
use Sweetchuck\Robo\Shellcheck\OutputParser\CheckJsonOutputParser;
use Sweetchuck\Robo\Shellcheck\OutputParser\VersionOutputParser;
use Sweetchuck\Robo\Shellcheck\OutputParserInterface;

abstract class CliCheckTask extends CliTask
{
    protected array $outputParserAssetNameMapping = [];

    public function __construct()
    {
        $this->taskName = 'ShellCheck - Check';
        $this->outputParserClass = VersionOutputParser::class;
    }

    // region checkSourced
    protected bool $checkSourced = false;

    public function getCheckSourced(): bool
    {
        return $this->checkSourced;
    }

    /**
     * @return $this
     */
    public function setCheckSourced(bool $checkSourced)
    {
        $this->checkSourced = $checkSourced;

        return $this;
    }
    // endregion

    // region include
    protected array $include = [];

    public function getInclude(): array
    {
        return $this->include;
    }

    /**
     * @return $this
     */
    public function setInclude(array $include)
    {
        if (gettype(reset($include)) !== 'boolean') {
            $include = array_fill_keys($include, true);
        }

        $this->include = $include;

        return $this;
    }
    // endregion

    // region exclude
    /**
     * @var array
     */
    protected $exclude = [];

    public function getExclude(): array
    {
        return $this->exclude;
    }

    /**
     * @return $this
     */
    public function setExclude(array $exclude)
    {
        if (gettype(reset($exclude)) !== 'boolean') {
            $exclude = array_fill_keys($exclude, true);
        }

        $this->exclude = $exclude;

        return $this;
    }
    // endregion

    // region format
    protected string $format = 'json';

    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return $this
     */
    public function setFormat(string $format)
    {
        $this->format = $format;

        return $this;
    }
    // endregion

    // region noRc
    protected bool $noRc = false;

    public function getNoRc(): bool
    {
        return $this->noRc;
    }

    /**
     * @return $this
     */
    public function setNoRc(bool $noRc)
    {
        $this->noRc = $noRc;

        return $this;
    }
    // endregion

    // region enable
    protected array $enable = [];

    public function getEnable(): array
    {
        return $this->enable;
    }

    /**
     * @return $this
     */
    public function setEnable(array $enable)
    {
        if (gettype(reset($enable)) !== 'boolean') {
            $enable = array_fill_keys($enable, true);
        }

        $this->enable = $enable;

        return $this;
    }
    // endregion

    // region sourcePath
    protected string $sourcePath = '';

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    /**
     * @return $this
     */
    public function setSourcePath(string $sourcePath)
    {
        $this->sourcePath = $sourcePath;

        return $this;
    }
    // endregion

    // region shell
    protected string $shell = '';

    public function getShell(): string
    {
        return $this->shell;
    }

    /**
     * @return $this
     */
    public function setShell(string $shell)
    {
        $this->shell = $shell;

        return $this;
    }
    // endregion

    // region severity
    protected string $severity = '';

    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * @return $this
     */
    public function setSeverity(string $severity)
    {
        $this->severity = $severity;

        return $this;
    }
    // endregion

    // region wikiLinkCount
    /**
     * @var null|int
     */
    protected ?int $wikiLinkCount = null;

    public function getWikiLinkCount(): ?int
    {
        return $this->wikiLinkCount;
    }

    /**
     * @return $this
     */
    public function setWikiLinkCount(?int $wikiLinkCount)
    {
        $this->wikiLinkCount = $wikiLinkCount;

        return $this;
    }
    // endregion

    // region externalSources
    protected bool $externalSources = false;

    public function getExternalSources(): bool
    {
        return $this->externalSources;
    }

    /**
     * @return $this
     */
    public function setExternalSources(bool $externalSources)
    {
        $this->externalSources = $externalSources;

        return $this;
    }
    // endregion

    // region lintReporters
    /**
     * @var \Sweetchuck\LintReport\ReporterInterface[]
     */
    protected array $lintReporters = [];

    /**
     * @return bool[]|\Sweetchuck\LintReport\ReporterInterface[]
     */
    public function getLintReporters(): array
    {
        return $this->lintReporters;
    }

    /**
     * @param null[]|string[]|false[]|\Sweetchuck\LintReport\ReporterInterface[]
     *
     * @return $this
     */
    public function setLintReporters(array $lintReporters)
    {
        $this->lintReporters = $lintReporters;

        return $this;
    }

    /**
     * @param string $id
     * @param null|string|\Sweetchuck\LintReport\ReporterInterface $lintReporter
     *
     * @return $this
     */
    public function addLintReporter(string $id, $lintReporter = null)
    {
        $this->lintReporters[$id] = $lintReporter;

        return $this;
    }

    /**
     * @return $this
     */
    public function removeLintReporter(string $id)
    {
        unset($this->lintReporters[$id]);

        return $this;
    }
    // endregion

    protected array $initializedLintReporters = [];

    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (array_key_exists('checkSourced', $options)) {
            $this->setCheckSourced($options['checkSourced']);
        }

        if (array_key_exists('include', $options)) {
            $this->setInclude($options['include']);
        }

        if (array_key_exists('exclude', $options)) {
            $this->setExclude($options['exclude']);
        }

        if (array_key_exists('format', $options)) {
            $this->setFormat($options['format']);
        }

        if (array_key_exists('noRc', $options)) {
            $this->setNoRc($options['noRc']);
        }

        if (array_key_exists('enable', $options)) {
            $this->setEnable($options['enable']);
        }

        if (array_key_exists('sourcePath', $options)) {
            $this->setSourcePath($options['sourcePath']);
        }

        if (array_key_exists('shell', $options)) {
            $this->setShell($options['shell']);
        }

        if (array_key_exists('severity', $options)) {
            $this->setSeverity($options['severity']);
        }

        if (array_key_exists('wikiLinkCount', $options)) {
            $this->setWikiLinkCount($options['wikiLinkCount']);
        }

        if (array_key_exists('externalSources', $options)) {
            $this->setExternalSources($options['externalSources']);
        }

        if (array_key_exists('lintReporters', $options)) {
            $this->setLintReporters($options['lintReporters']);
        }

        return $this;
    }

    protected function getCommandOptions(): array
    {
        return [
            'checkSourced' => [
                'type' => 'option:flag',
                'name' => 'check-sourced',
                'value' => $this->getCheckSourced(),
            ],
            'include' => [
                'type' => 'option:list',
                'value' => $this->getInclude(),
            ],
            'exclude' => [
                'type' => 'option:list',
                'value' => $this->getExclude(),
            ],
            'format' => [
                'type' => 'option:value',
                'value' => $this->getFormat(),
            ],
            'noRc' => [
                'type' => 'option:flag',
                'name' => 'norc',
                'value' => $this->getNoRc(),
            ],
            'enable' => [
                'type' => 'option:list',
                'value' => $this->getEnable(),
            ],
            'sourcePath' => [
                'type' => 'option:value',
                'name' => 'source-path',
                'value' => $this->getSourcePath(),
            ],
            'shell' => [
                'type' => 'option:value',
                'value' => $this->getShell(),
            ],
            'severity' => [
                'type' => 'option:value',
                'value' => $this->getSeverity(),
            ],
            'wikiLinkCount' => [
                'type' => 'option:value',
                'name' => 'wiki-link-count',
                'value' => $this->getWikiLinkCount(),
            ],
            'externalSources' => [
                'type' => 'option:flag',
                'name' => 'external-sources',
                'value' => $this->getExternalSources(),
            ],
        ] + parent::getCommandOptions();
    }

    protected function runInit()
    {
        parent::runInit();
        $this->initLintReporters();

        return $this;
    }

    protected function getProcessRunCallbackWrapper(): \Closure
    {
        if ($this->getFormat() === 'json' && $this->initializedLintReporters) {
            return function (string $type, string $data): void {
                // Nothing to do.
            };
        }

        return parent::getProcessRunCallbackWrapper();
    }

    protected function runProcessOutputs()
    {
        parent::runProcessOutputs();
        if (empty($this->assets['shellcheck.check.report'])) {
            return $this;
        }

        $reportWrapper = $this->assets['shellcheck.check.report'];
        foreach ($this->initializedLintReporters as $lintReporter) {
            $lintReporter->setReportWrapper($reportWrapper);
            $lintReporter->generate();
        }

        return $this;
    }

    protected function getOutputParser(): ?OutputParserInterface
    {
        if ($this->getFormat() === 'json') {
            $parser = new CheckJsonOutputParser();
            $parser->setAssetNameMapping([
                'report' => 'shellcheck.check.report',
            ]);

            return $parser;
        }

        return null;
    }

    /**
     * @return \Sweetchuck\LintReport\ReporterInterface[]
     */
    protected function initLintReporters()
    {
        $this->initializedLintReporters = [];
        $c = $this->getContainer();
        foreach ($this->getLintReporters() as $id => $lintReporter) {
            if ($lintReporter === false) {
                continue;
            }

            if (!$lintReporter) {
                $lintReporter = $c->get($id);
            } elseif (is_string($lintReporter)) {
                $lintReporter = $c->get($lintReporter);
            }

            if ($lintReporter instanceof ReporterInterface) {
                $this->initializedLintReporters[$id] = $lintReporter;
                if (!$lintReporter->getDestination()) {
                    $lintReporter
                        ->setFilePathStyle('relative')
                        ->setDestination($this->output());
                }
            }
        }

        return $this;
    }
}
