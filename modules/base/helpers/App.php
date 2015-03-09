<?php

class Base_Helper_App extends Base_Php_Overloader
{
    static public function _sureContainer($file)
    {
        $target = dirname($file);
        if (!is_dir($target) && !mkdir($target, 0777, TRUE)) {
            return '';
        }
        return $file;
    }


    static public function clearTemp()
    {
        try {
            $tempPath = Base_Constant_Server::getFirstTemp();
            if (!is_dir($tempPath)) {
                return;
            }
            $dirIterator = new RecursiveDirectoryIterator($tempPath);
            $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $path) {
                switch (true) {
                    case '.' == basename($path->__toString()):
                    case '..' == basename($path->__toString()):
                    case '...' == basename($path->__toString()):
                        break;
                    case $path->isDir():
                        rmdir($path->__toString());
                        break;
                    default:
                        unlink($path->__toString());
                        break;
                }
            }

        } catch (Exception $e) {
            throw $e;
        }
    }

    static public function clearCache()
    {
        if (!is_dir(CACHE_PATH)) {
            return;
        }
        try {
            $dirIterator = new RecursiveDirectoryIterator(CACHE_PATH);
            $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $path) {
                switch (true) {
                    case '.' == basename($path->__toString()):
                    case '..' == basename($path->__toString()):
                    case '...' == basename($path->__toString()):
                        break;
                    case $path->isDir():
                        rmdir($path->__toString());
                        break;
                    default:
                        unlink($path->__toString());
                        break;
                }
            }
            rmdir(CACHE_PATH);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
