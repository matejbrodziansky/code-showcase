<?php

namespace AppTranscriptionBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FileService
{
    private Filesystem $filesystem;
    private string $audioFilesDirectory;
    private string $doneDirectory;

    private LoggerInterface $logger;
    const METADATA_PHONE_NUMBER_KEY = 'callerid';
    const METADATA_ORIGIN_DATE_KEY = 'origdate';

    public function __construct(
        Filesystem      $filesystem,
        string          $audioFilesDirectory,
        string          $doneDirectory,
        LoggerInterface $logger
    )
    {
        $this->filesystem = $filesystem;
        $this->audioFilesDirectory = $audioFilesDirectory;
        $this->logger = $logger;
        $this->doneDirectory = $doneDirectory;
    }

    public function getWavFiles(): array
    {
        if (!$this->filesystem->exists($this->audioFilesDirectory)) {
            throw new \Exception("Directory does not exist.");
        }

        $files = scandir($this->audioFilesDirectory);

        return array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'wav';
        });
    }

    private function getValueFromMetadata(string $fileName, string $key): ?string
    {
        $txtFile = $this->getTxtFilePath($fileName);

        if (!$this->filesystem->exists($txtFile)) {
            throw new \Exception("Metadata file does not exist for " . $fileName);
        }

        $content = file_get_contents($txtFile);
        if ($content === false) {
            throw new \Exception("Failed to read metadata file " . $txtFile);
        }

        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $parts = explode("=", $line, 2);
            if (count($parts) === 2 && trim($parts[0]) === $key) {
                return trim($parts[1]);
            }
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    public function getCallerPhoneFromMetadata(string $fileName): ?string
    {
        return $this->getValueFromMetadata($fileName, self::METADATA_PHONE_NUMBER_KEY);
    }


    /**
     * @throws \Exception
     */
    public function getCreatedAtFromMetadata(string $fileName): ?string
    {
        return $this->getValueFromMetadata($fileName, self::METADATA_ORIGIN_DATE_KEY);
    }

    public function getFilePath(string $fileName): string
    {
        return $this->audioFilesDirectory . DIRECTORY_SEPARATOR . $fileName;
    }

    private function getTxtFilePath(string $fileName): string
    {
        return $this->audioFilesDirectory . DIRECTORY_SEPARATOR . pathinfo($fileName, PATHINFO_FILENAME) . '.txt';
    }

    public function moveFileToDone(string $filePath): void
    {
        try {
            if (!$this->filesystem->exists($this->doneDirectory)) {
                $this->filesystem->mkdir($this->doneDirectory);
            }

            $filename = basename($filePath);
            $newPath = $this->doneDirectory . DIRECTORY_SEPARATOR . $filename;
            $this->filesystem->copy($filePath, $newPath);

            $txtFilePath = $this->getTxtFilePath($filename);
            if ($this->filesystem->exists($txtFilePath)) {
                $newTxtPath = $this->doneDirectory . DIRECTORY_SEPARATOR . pathinfo($filename, PATHINFO_FILENAME) . '.txt';
                $this->filesystem->copy($txtFilePath, $newTxtPath);
                $this->removeFile($txtFilePath);
            }

        } catch (\Exception $e) {
            $this->logger->error('Failed to move file to done directory', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function removeFile(string $filePath): void
    {
        $this->filesystem->remove($filePath);
    }

    /**
     * @throws \Exception
     */
    public function cleanUpOldFiles(int $days = 14): bool
    {
        $dateThreshold = new \DateTime('-' . $days . ' days');

        if (!$this->filesystem->exists($this->doneDirectory)) {
            $this->logger->error("Base directory does not exist: $this->doneDirectory");
            return false;
        }

        $finder = new Finder();
        $finder->files()->in($this->doneDirectory)->date('< ' . $dateThreshold->format('Y-m-d'));

        foreach ($finder as $file) {
            $filePath = $file->getRealPath();
            try {
                $this->filesystem->remove($filePath);
                $this->logger->info("Removed file: $filePath");
            } catch (\Exception $e) {
                $message = "Error removing file $filePath: " . $e->getMessage();
                $this->logger->error($message);
                throw new \Exception($message);
            }
        }

        return true;
    }

    private function removeDirectory($dirPath): void
    {
        if (!is_dir($dirPath)) {
            $this->logger->error("Directory does not exist: $dirPath");
            throw new \RuntimeException("Directory does not exist: $dirPath");
        }

        $items = new \FilesystemIterator($dirPath, \FilesystemIterator::SKIP_DOTS);

        foreach ($items as $item) {
            if ($item->isDir()) {
                $this->removeDirectory($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        rmdir($dirPath);
    }
}