<?php

namespace Botble\LogViewer\Entities;

use Botble\LogViewer\Helpers\LogParser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LogEntryCollection extends Collection
{
    /**
     * Load raw log entries.
     *
     * @param string $raw
     *
     * @return self
     */
    public function load($raw)
    {
        foreach (LogParser::parse($raw) as $entry) {
            [$level, $header, $stack] = array_values($entry);

            $this->push(new LogEntry($level, $header, $stack));
        }

        return $this;
    }

    /**
     * Paginate log entries.
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = 20)
    {
        $request = request();
        $page = $request->input('page', 1);
        $paginator = new LengthAwarePaginator(
            $this->slice(($page * $perPage) - $perPage, $perPage),
            $this->count(),
            $perPage,
            $page
        );

        return $paginator->setPath($request->url());
    }

    /**
     * Get filtered log entries by level.
     *
     * @param string $level
     *
     * @return LogEntryCollection
     */
    public function filterByLevel($level)
    {
        return $this->filter(function (LogEntry $entry) use ($level) {
            return $entry->isSameLevel($level);
        });
    }

    /**
     * Get log entries stats.
     *
     * @return array
     */
    public function stats()
    {
        $counters = $this->initStats();

        foreach ($this->groupBy('level') as $level => $entries) {
            $counters[$level] = $count = count($entries);
            $counters['all'] += $count;
        }

        return $counters;
    }

    /**
     * Get the log entries navigation tree.
     *
     * @param bool|false $trans
     *
     * @return array
     */
    public function tree($trans = false)
    {
        $tree = $this->stats();

        array_walk($tree, function (&$count, $level) use ($trans) {
            $count = [
                'name'  => $trans ? log_levels()->get($level) : $level,
                'count' => $count,
            ];
        });

        return $tree;
    }

    /**
     * Init stats counters.
     *
     * @return array
     */
    protected function initStats()
    {
        $levels = array_merge_recursive(
            ['all'],
            array_keys(log_viewer()->levels(true))
        );

        return array_map(function () {
            return 0;
        }, array_flip($levels));
    }
}
