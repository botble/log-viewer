<?php

namespace Botble\LogViewer\Contracts;

use Botble\LogViewer\Entities\LogEntryCollection;
use Botble\LogViewer\Entities\Log;
use Botble\LogViewer\Entities\LogCollection;
use Botble\LogViewer\Exceptions\FilesystemException;
use Botble\LogViewer\Tables\StatsTable;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface LogViewer extends Patternable
{
    /**
     * Get the log levels.
     *
     * @param bool|false $flip
     *
     * @return array
     */
    public function levels($flip = false);

    /**
     * Get the translated log levels.
     *
     * @param string|null $locale
     *
     * @return array
     */
    public function levelsNames($locale = null);

    /**
     * Set the log storage path.
     *
     * @param string $path
     *
     * @return \Botble\LogViewer\LogViewer
     */
    public function setPath($path);

    /**
     * Get all logs.
     *
     * @return LogCollection
     */
    public function all();

    /**
     * Paginate all logs.
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = 30);

    /**
     * Get a log.
     *
     * @param string $date
     *
     * @return Log
     */
    public function get($date);

    /**
     * Get the log entries.
     *
     * @param string $date
     * @param string $level
     *
     * @return LogEntryCollection
     */
    public function entries($date, $level = 'all');

    /**
     * Download a log file.
     *
     * @param string $date
     * @param string|null $filename
     * @param array $headers
     *
     * @return BinaryFileResponse
     */
    public function download($date, $filename = null, $headers = []);

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
     *
     * @return StatsTable
     */
    public function statsTable($locale = null);

    /**
     * Delete the log.
     *
     * @param string $date
     *
     * @return bool
     *
     * @throws FilesystemException
     */
    public function delete($date);

    /**
     * List the log files.
     *
     * @return array
     */
    public function files();

    /**
     * List the log files (only dates).
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
     * Get entries total from all logs.
     *
     * @param string $level
     *
     * @return int
     */
    public function total($level = 'all');

    /**
     * Get logs tree.
     *
     * @param bool|false $trans
     *
     * @return array
     */
    public function tree($trans = false);

    /**
     * Get logs menu.
     *
     * @param bool|true $trans
     *
     * @return array
     */
    public function menu($trans = true);

    /**
     * Determine if the log folder is empty or not.
     *
     * @return bool
     */
    public function isEmpty();
}
