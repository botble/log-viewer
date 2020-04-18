<?php

namespace Botble\LogViewer\Utilities;

use Botble\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Botble\LogViewer\Exceptions\FilesystemException;
use Exception;
use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;

class Filesystem implements FilesystemContract
{
    /**
     * The filesystem instance.
     *
     * @var IlluminateFilesystem
     */
    protected $filesystem;

    /**
     * The base storage path.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * The log files prefix pattern.
     *
     * @var string
     */
    protected $prefixPattern;

    /**
     * The log files date pattern.
     *
     * @var string
     */
    protected $datePattern;

    /**
     * The log files extension.
     *
     * @var string
     */
    protected $extension;

    /**
     * Filesystem constructor.
     *
     * @param IlluminateFilesystem $files
     * @param string $storagePath
     */
    public function __construct(IlluminateFilesystem $files, $storagePath)
    {
        $this->filesystem = $files;
        $this->setPath($storagePath);
        $this->setPattern();
    }

    /**
     * Get the files instance.
     *
     * @return IlluminateFilesystem
     */
    public function getInstance()
    {
        return $this->filesystem;
    }

    /**
     * Set the log storage path.
     *
     * @param string $storagePath
     *
     * @return Filesystem
     */
    public function setPath($storagePath)
    {
        $this->storagePath = $storagePath;

        return $this;
    }

    /**
     * Get the log pattern.
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->prefixPattern . $this->datePattern . $this->extension;
    }

    /**
     * Set the log pattern.
     *
     * @param string $date
     * @param string $prefix
     * @param string $extension
     *
     * @return Filesystem
     */
    public function setPattern(
        $prefix = self::PATTERN_PREFIX,
        $date = self::PATTERN_DATE,
        $extension = self::PATTERN_EXTENSION
    ) {
        $this->setPrefixPattern($prefix);
        $this->setDatePattern($date);
        $this->setExtension($extension);

        return $this;
    }

    /**
     * Set the log date pattern.
     *
     * @param string $datePattern
     *
     * @return Filesystem
     */
    public function setDatePattern($datePattern)
    {
        $this->datePattern = $datePattern;

        return $this;
    }

    /**
     * Set the log prefix pattern.
     *
     * @param string $prefixPattern
     *
     * @return Filesystem
     */
    public function setPrefixPattern($prefixPattern)
    {
        $this->prefixPattern = $prefixPattern;

        return $this;
    }

    /**
     * Set the log extension.
     *
     * @param string $extension
     *
     * @return Filesystem
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get all log files.
     *
     * @return array
     */
    public function all()
    {
        return $this->getFiles('*' . $this->extension);
    }

    /**
     * Get all valid log files.
     *
     * @return array
     */
    public function logs()
    {
        return $this->getFiles($this->getPattern());
    }

    /**
     * List the log files (Only dates).
     *
     * @param bool $withPaths
     *
     * @return array
     */
    public function dates($withPaths = false)
    {
        $files = array_reverse($this->logs());
        $dates = $this->extractDates($files);

        if ($withPaths) {
            $dates = array_combine($dates, $files); // [date => file]
        }

        return $dates;
    }

    /**
     * Read the log.
     *
     * @param string $date
     *
     * @return string
     *
     * @throws FilesystemException
     */
    public function read($date)
    {
        try {
            $path = $this->getLogPath($date);

            return $this->filesystem->get($path);
        } catch (Exception $exception) {
            throw new FilesystemException($exception->getMessage());
        }
    }

    /**
     * Delete the log.
     *
     * @param string $date
     *
     * @return bool
     *
     * @throws FilesystemException
     */
    public function delete($date)
    {
        $path = $this->getLogPath($date);

        // @codeCoverageIgnoreStart
        if (!$this->filesystem->delete($path)) {
            throw new FilesystemException('There was an error deleting the log.');
        }
        // @codeCoverageIgnoreEnd

        return true;
    }

    /**
     * Get the log file path.
     *
     * @param string $date
     *
     * @return string
     * @throws FilesystemException
     */
    public function path($date)
    {
        return $this->getLogPath($date);
    }

    /**
     * Get all files.
     *
     * @param string $pattern
     *
     * @return array
     */
    private function getFiles($pattern)
    {
        $files = $this->filesystem->glob(
            $this->storagePath . DIRECTORY_SEPARATOR . $pattern,
            GLOB_BRACE
        );

        return array_filter(array_map('realpath', $files));
    }

    /**
     * Get the log file path.
     *
     * @param string $date
     *
     * @return string
     *
     * @throws FilesystemException
     */
    private function getLogPath($date)
    {
        $path = $this->storagePath . DIRECTORY_SEPARATOR . $this->prefixPattern . $date . $this->extension;

        if (!$this->filesystem->exists($path)) {
            throw new FilesystemException('The log(s) could not be located at : ' . $path);
        }

        return realpath($path);
    }

    /**
     * Extract dates from files.
     *
     * @param array $files
     *
     * @return array
     */
    private function extractDates(array $files)
    {
        return array_map(function ($file) {
            return extract_date(basename($file));
        }, $files);
    }
}
