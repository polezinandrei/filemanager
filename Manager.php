<?php

class Manager
{
    private function setErrorHandler() {
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, $severity, $severity, $file, $line);
        });
    }

    private function restoreErrorHandler() {
        restore_error_handler();
    }

    private function convertPath($realpath) {
        $tmp = explode('/', $realpath);
        if ($tmp[0] == $realpath) $tmp = explode('\\', $realpath);

        return implode('/', $tmp);
    }

    public function creadeDir($directory) {
        $this->setErrorHandler();

        try {
            if (mkdir($directory, 755)) {
                $this->restoreErrorHandler();

                return ['message' => 'Директория успешно создана'];
            }
        } catch (ErrorException $e) {
            $this->restoreErrorHandler();

            return ['message' => 'Не удалось создать директорию!', 'err' => '1'];
        }
    }

    public function deleteElement($path, $isDir) {
        $this->setErrorHandler();

        if ($isDir == 'true') {
            try {
                if (rmdir($path)) {
                    $this->restoreErrorHandler();

                    return ['message' => 'Директория успешно удалена'];
                }
            } catch (ErrorException $e) {
                $this->restoreErrorHandler();

                return ['message' => 'Не удалось удалить директорию!', 'err' => '1'];
            }
        }

        try {
            if (unlink($path)) {
                $this->restoreErrorHandler();

                return ['message' => 'Файл успешно удален'];
            }
        } catch (ErrorException $e) {
            $this->restoreErrorHandler();

            return ['message' => 'Не удалось удалить файл!', 'err' => '1'];
        }
    }

    public function renameElement($from, $to) {
        $this->setErrorHandler();

        try {
            if (rename($from, $to)) {
                $this->restoreErrorHandler();

                return ['message' => 'Успешно переименовано'];
            }
        } catch (ErrorException $e) {
            $this->restoreErrorHandler();

            return ['message' => 'Не удалось переименовать!', 'err' => '1'];
        }
    }

    public function getList($dir = NULL) {
        if (!isset($dir) || !is_dir($dir))
            $dir = $_SERVER['DOCUMENT_ROOT'];
        $files = scandir($dir);
        $list = [];

        foreach ($files as $key => $value) {
            $realpath = realpath($dir . '/' . $value);
            $path = $this->convertPath($realpath);
            $url = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);

            $list[$key] = [
                'dir'     => is_dir($realpath) ? 'true' : 'false',
                'name'    => $value,
                'path'    => $path,
                'url'     => $url,
                'current' => $dir,
            ];
        }

        return $list;
    }
}