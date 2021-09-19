<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Helper\RoboFiles;

use League\Container\Container as LeagueContainer;
use Robo\Tasks;
use Sweetchuck\LintReport\Reporter\BaseReporter;
use Sweetchuck\Robo\Shellcheck\ShellcheckTaskLoader;
use Robo\State\Data as RoboStateData;
use Symfony\Component\Yaml\Yaml;

class ShellcheckRoboFile extends Tasks
{
    use ShellcheckTaskLoader;

    protected function output()
    {
        return $this->getContainer()->get('output');
    }


    /**
     * @hook pre-command @initLintReporters
     */
    public function initLintReporters()
    {
        $lintServices = BaseReporter::getServices();
        $container = $this->getContainer();
        foreach ($lintServices as $name => $class) {
            if ($container->has($name)) {
                continue;
            }

            if ($container instanceof LeagueContainer) {
                $container->share($name, $class);
            }
        }
    }

    /**
     * @command shellcheck:version
     */
    public function cmdShellcheckVersionExecute()
    {
        return $this
            ->collectionBuilder()
            ->addTask($this->taskShellcheckVersion())
            ->addCode(function (RoboStateData $data): int {
                $raw = $data->getData();
                unset($raw['time']);
                if (!empty($raw['shellcheck.version.version'])) {
                    $raw['shellcheck.version.version'] = '9.9.9';
                }
                $this->output()->write(Yaml::dump($raw, 42));

                return 0;
            });
    }

    /**
     * @command shellcheck:list-optional
     */
    public function cmdShellcheckListOptionalExecute()
    {
        return $this
            ->collectionBuilder()
            ->addTask($this->taskShellcheckListOptional())
            ->addCode(function (RoboStateData $data): int {
                $raw = $data->getData();
                unset($raw['time']);
                $this->output()->write(Yaml::dump($raw, 42));

                return 0;
            });
    }

    /**
     * @command shellcheck:check:files
     *
     * @initLintReporters
     */
    public function cmdShellcheckCheckFilesExecute(array $files)
    {
        return $this
            ->collectionBuilder()
            ->addTask($this
                ->taskShellcheckCheckFiles()
                ->setOutput($this->output())
                ->setLintReporters([
                    'lintVerboseReporter' => null,
                ])
                ->setFiles($files));
    }

    /**
     * @command shellcheck:check:input
     *
     * @initLintReporters
     */
    public function cmdShellcheckCheckInputExecute(string $filesJson)
    {
        $files = json_decode($filesJson, true);

        return $this
            ->collectionBuilder()
            ->addTask($this
                ->taskShellcheckCheckInput()
                ->setOutput($this->output())
                ->setLintReporters([
                    'lintVerboseReporter' => null,
                ])
                ->setFiles($files));
    }
}
