<?php

namespace Botble\LogViewer\Contracts\Utilities;

use Botble\LogViewer\Contracts\Patternable;
use Botble\LogViewer\Entities\Log;
use Botble\LogViewer\Entities\LogCollection;
use Botble\LogViewer\Entities\LogEntryCollection;
use Botble\LogViewer\Tables\StatsTable;
use Illuminate\Pagination\LengthAwarePaginator;

interface Factory extends Patternable
{
    /**
     * Get the filesystem instance.
     *
     * @return Filesystem
     */
    public function getFilesystem();

    /**
     * Set the filesystem instance.
     *
     * @param Filesystem $filesystem
     *
     * @return self
     */
    public function setFilesystem(Filesystem $filesystem);

    /**
     * Get the log levels instance.
     *
     * @return  LogLevels  $levels
     */
    public function getLevels();

    /**
     * Set the log levels instance.
     *
     * @param LogLevels $levels
     * @return self
     */
    public function setLevels(LogLevels $levels);

    /**
     * Set the log storage path.
     *
     * @param string $storagePath
     * @return self
     */
    public function setPath($storagePath);

    /**
     * Get all logs.
     *
     * @return LogCollection
     */
    public function logs();

    /**
     * Get all logs (alias).
     *
     * @return LogCollection
     */
    public function all();

    /**
     * Paginate all logs.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = 30);

    /**
     * Get a log by date.
     *
     * @param string $date
     * @return Log
     */
    public function log($date);

    /**
     * Get a log by date (alias).
     *
     * @param string $date
     * @return Log
     */
    public function get($date);

    /**
     * Get log entries.
     *
     * @param string $date
     * @param string $level
     * @return LogEntryCollection
     */
    public function entries($date, $level = 'all');

    /**
     * List the log files (dates).
     *
     * @return array
     */
    public function dates();

    /**
     * Get logs count.
     *
     * @return int
     */
    public function count();

    /**
     * Get total log entries.
     *
     * @param string $level
     * @return int
     */
    public function total($level = 'all');

    /**
     * Get tree menu.
     *
     * @param bool $trans
     * @return array
     */
    public function tree($trans = false);

    /**
     * Get tree menu.
     *
     * @param bool $trans
     * @return array
     */
    public function menu($trans = true);

    /**
     * Get logs statistics.
     *
     * @return array
     */
    public function stats();

    /**
     * Get logs statistics table.
     *
     * @param string|null $locale
     * @return StatsTable
     */
    public function statsTable($locale = null);

    /**
     * Determine if the log folder is empty or not.
     *
     * @return bool
     */
    public function isEmpty();
}
