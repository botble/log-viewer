<?php

namespace Botble\LogViewer\Tables;

use Botble\LogViewer\Bases\Table;
use Botble\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Illuminate\Support\Arr;

class StatsTable extends Table
{
    public static function make(array $data, LogLevelsContract $levels, string $locale = null): static
    {
        return new self($data, $levels, $locale);
    }

    public function totalsJson(string $locale = null): bool|array|string
    {
        $this->setLocale($locale);

        $json = [];
        $levels = Arr::except($this->footer(), 'all');

        foreach ($levels as $level => $count) {
            $json[] = [
                'label'     => $this->translate('levels.' . $level),
                'value'     => $count,
                'color'     => $this->color($level),
                'highlight' => $this->color($level),
            ];
        }

        return json_encode(array_values($json), JSON_PRETTY_PRINT);
    }

    protected function prepareHeader(array $data): array
    {
        return array_merge_recursive(
            [
                'date' => $this->translate('general.date'),
                'all'  => $this->translate('general.all'),
            ],
            $this->levels->names($this->locale)
        );
    }

    protected function prepareRows(array $data): array
    {
        $rows = [];

        foreach ($data as $date => $levels) {
            $rows[$date] = array_merge(compact('date'), $levels);
        }

        return $rows;
    }

    protected function prepareFooter(array $data): array
    {
        $footer = [];

        foreach ($data as $levels) {
            foreach ($levels as $level => $count) {
                if (!isset($footer[$level])) {
                    $footer[$level] = 0;
                }

                $footer[$level] += $count;
            }
        }

        return $footer;
    }
}
