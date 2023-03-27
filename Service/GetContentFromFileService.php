<?php

declare(strict_types=1);

namespace BurlacuWeb\CmsContentHandling\Service;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Class GetContentFromFileService
 * Reads content from given file path.
 */
class GetContentFromFileService
{
    /**
     * @var File
     */
    private File $fileReader;

    public function __construct(
        File $fileReader
    ) {
        $this->fileReader = $fileReader;
    }

    /**
     * Reads content from given file path.
     *
     * @param string $filePath
     *
     * @return string
     * @throws FileSystemException
     */
    public function execute(string $filePath): string
    {
        if ($this->fileReader->isFile($filePath)) {
            return $this->fileReader->fileGetContents($filePath);
        }

        return '';
    }
}
