<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck;

use League\Container\ContainerAwareInterface;
use Robo\Collection\CollectionBuilder;

trait ShellcheckTaskLoader
{
    /**
     * @return \Sweetchuck\Robo\Shellcheck\Task\CliVersionTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskShellcheckVersion(array $options = []): CollectionBuilder
    {
        /** @var \Sweetchuck\Robo\Shellcheck\Task\CliVersionTask|\Robo\Collection\CollectionBuilder $task */
        $task = $this->task(Task\CliVersionTask::class);
        if ($this instanceof ContainerAwareInterface) {
            $task->setContainer($this->getContainer());
        }

        $task->setOptions($options);

        return $task;
    }

    /**
     * @return \Sweetchuck\Robo\Shellcheck\Task\CliListOptionalTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskShellcheckListOptional(array $options = []): CollectionBuilder
    {
        /** @var \Sweetchuck\Robo\Shellcheck\Task\CliListOptionalTask|\Robo\Collection\CollectionBuilder $task */
        $task = $this->task(Task\CliListOptionalTask::class);
        if ($this instanceof ContainerAwareInterface) {
            $task->setContainer($this->getContainer());
        }

        $task->setOptions($options);

        return $task;
    }

    /**
     * @return \Sweetchuck\Robo\Shellcheck\Task\CliCheckFilesTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskShellcheckCheckFiles(array $options = []): CollectionBuilder
    {
        /** @var \Sweetchuck\Robo\Shellcheck\Task\CliCheckFilesTask|\Robo\Collection\CollectionBuilder $task */
        $task = $this->task(Task\CliCheckFilesTask::class);
        if ($this instanceof ContainerAwareInterface) {
            $task->setContainer($this->getContainer());
        }

        $task->setOptions($options);

        return $task;
    }

    /**
     * @return \Sweetchuck\Robo\Shellcheck\Task\CliCheckInputTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskShellcheckCheckInput(array $options = []): CollectionBuilder
    {
        /** @var \Sweetchuck\Robo\Shellcheck\Task\CliCheckInputTask|\Robo\Collection\CollectionBuilder $task */
        $task = $this->task(Task\CliCheckInputTask::class);
        if ($this instanceof ContainerAwareInterface) {
            $task->setContainer($this->getContainer());
        }

        $task->setOptions($options);

        return $task;
    }
}
