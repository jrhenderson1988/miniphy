<?php

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Create a Miniphy instance.
     *
     * @return \Miniphy\Miniphy
     */
    public function createMiniphyInstance()
    {
        return new Miniphy\Miniphy();
    }

    /**
     * Load the provided file into a string and trim it's contents. Return false on error.
     *
     * @param string $path
     *
     * @return bool|string
     */
    public function loadFile($path)
    {
        $path = rtrim(__DIR__, '/') . '/' . ltrim($path, '/');

        if (!is_readable($path)) {
            return false;
        }

        return trim(file_get_contents($path));
    }

    /**
     * Get a list of directory names inside the provided directory. The directory provided is considered to be relative
     * to the tests directory and any leading forward slashes will be stripped off.
     *
     * @param string $dir
     *
     * @return array
     */
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
