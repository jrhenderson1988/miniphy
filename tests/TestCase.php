<?php

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createMiniphyInstance()
    {
        return new Miniphy\Miniphy();
    }

    public function loadFile($path)
    {
        $path = rtrim(__DIR__, '/') . '/' . ltrim($path, '/');

        if (!is_readable($path)) {
            return false;
        }

        return trim(file_get_contents($path));
    }

    public function directoriesIn($dir)
    {
        $directories = [];

        $path = rtrim(__DIR__, '/') . '/' . ltrim($dir, '/');
        if ($handle = opendir($path)) {
            while (($entry = readdir($handle)) !== false) {
                if ($entry != '.' && $entry != '..' && is_dir($path . '/' . $entry)) {
                    $directories[] = $entry;
                }
            }
        }

        return $directories;
    }
}
