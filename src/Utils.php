<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck;

class Utils
{

    public static function detectShellDialog(array $file): ?string
    {
        if (isset($file['fileName'])) {
            $dialog = static::detectShellDialogFromFilePath($file['fileName']);
            if ($dialog !== null) {
                return $dialog;
            }
        }

        if (isset($file['content'])) {
            $dialog = static::detectShellDialogFromContent($file['content']);
            if ($dialog !== null) {
                return $dialog;
            }
        }

        return null;
    }

    public static function detectShellDialogFromFilePath(string $filePath): ?string
    {
        $parts = pathinfo($filePath);
        if (isset($parts['filename'])
            && $parts['filename'] !== ''
            && isset($parts['extension'])
            && $parts['extension'] !== ''
        ) {
            return $parts['extension'];
        }

        return null;
    }

    public static function detectShellDialogFromContent(string $content): ?string
    {
        $line = explode("\n", $content, 2)[0];
        $matches = [];
        preg_match(
            '@#!(?P<executable>[\S]+)(\s+(?P<arg1>[\S]+))?@',
            $line,
            $matches,
        );

        if (isset($matches['executable'])
            && preg_match('@/env$@', $matches['executable'])
            && isset($matches['arg1'])
        ) {
            return $matches['arg1'];
        }

        if (isset($matches['executable'])) {
            $parts = explode('/', $matches['executable']);

            return (string) end($parts);
        }

        return null;
    }
}
