<?php

namespace API\src\utilities\classes;

use Exception;
use InvalidArgumentException;

class Cache
{
    private $cacheDir;
    private $cacheTime;

    public function __construct($cacheDir = __DIR__ . '/../../var/cache/', $cacheTime = 10)
    {
        $this->setCacheDir($cacheDir);
        $this->setCacheTime($cacheTime);
    }

    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = rtrim($cacheDir, '/') . '/';
        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0755, true)) {
                throw new Exception("Unable to create cache directory: " . $this->cacheDir);
            }
        }
    }

    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    public function setCacheTime($cacheTime)
    {
        if (!is_int($cacheTime) || $cacheTime <= 0) {
            throw new InvalidArgumentException("Cache time must be a positive integer.");
        }
        $this->cacheTime = $cacheTime;
    }

    public function getCacheTime()
    {
        return $this->cacheTime;
    }

    private function getCacheFilePath($key)
    {
        return $this->cacheDir . md5($key) . '.cache';
    }

    public function set($key, $data)
    {
        $filePath = $this->getCacheFilePath($key);
        $cacheData = [
            'data' => serialize($data),
            'timestamp' => time()
        ];
        if (file_put_contents($filePath, serialize($cacheData)) === false) {
            throw new Exception("Failed to write cache data.");
        }
    }

    public function get($key)
    {
        $filePath = $this->getCacheFilePath($key);

        if (!file_exists($filePath)) {
            return false;
        }

        $cacheData = unserialize(file_get_contents($filePath));

        if ($cacheData === false) {
            unlink($filePath);
            throw new Exception("Failed to unserialize cache data.");
        }

        if (time() - $cacheData['timestamp'] > $this->cacheTime) {
            $this->delete($key);
            return false;
        }

        return unserialize($cacheData['data']);
    }

    public function delete($key)
    {
        $filePath = $this->getCacheFilePath($key);

        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                throw new Exception("Failed to delete cache file: " . $filePath);
            }
        }
    }

    public function clear()
    {
        $files = glob($this->cacheDir . '*.cache');
        $errors = [];

        foreach ($files as $file) {
            if (!unlink($file)) {
                $errors[] = $file;
            }
        }

        if (!empty($errors)) {
            throw new Exception("Failed to delete cache files: " . implode(', ', $errors));
        }
    }

    public function isCached($key)
    {
        $filePath = $this->getCacheFilePath($key);
        return file_exists($filePath) && (time() - filemtime($filePath) <= $this->cacheTime);
    }

    public function cleanExpired()
    {
        $files = glob($this->cacheDir . '*.cache');
        foreach ($files as $file) {
            if (time() - filemtime($file) > $this->cacheTime) {
                unlink($file);
            }
        }
    }
}
