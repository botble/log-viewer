<?php

namespace Botble\LogViewer\Bases;

use Botble\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Botble\LogViewer\Contracts\Table as TableContract;

abstract class Table implements TableContract
{
    /**
     * @var array
     */
    protected $header = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var array
     */
    protected $footer = [];

    /**
     * @var string
     */
    protected $levels;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Create a table instance.
     *
     * @param array $data
     * @param LogLevelsContract $levels
     * @param string|null $locale
     */
    public function __construct(array $data, LogLevelsContract $levels, $locale = null)
    {
        $this->setLevels($levels);
        $this->setLocale(empty($locale) ? config('plugins.log-viewer.general.locale') : $locale);
        $this->setData($data);
        $this->init();
    }

    /**
     * Set LogLevels instance.
     *
     * @param LogLevelsContract $levels
     *
     * @return Table
     */
    protected function setLevels(LogLevelsContract $levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Set table locale.
     *
     * @param string|null $locale
     *
     * @return Table
     */
    protected function setLocale($locale)
    {
        if (empty($locale) || $locale === 'auto') {
            $locale = app()->getLocale();
        }

        $this->locale = $locale;

        return $this;
    }

    /**
     * Set table data.
     *
     * @param array $data
     *
     * @return self
     */
    private function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Prepare the table.
     */
    private function init()
    {
        $this->header = $this->prepareHeader($this->data);
        $this->rows = $this->prepareRows($this->data);
        $this->footer = $this->prepareFooter($this->data);
    }

    /**
     * Prepare table header.
     *
     * @param array $data
     *
     * @return array
     */
    abstract protected function prepareHeader(array $data);

    /**
     * Prepare table rows.
     *
     * @param array $data
     *
     * @return array
     */
    abstract protected function prepareRows(array $data);

    /**
     * Prepare table footer.
     *
     * @param array $data
     *
     * @return array
     */
    abstract protected function prepareFooter(array $data);

    /**
     * Get table header.
     *
     * @return array
     */
    public function header()
    {
        return $this->header;
    }

    /**
     * Get table rows.
     *
     * @return array
     */
    public function rows()
    {
        return $this->rows;
    }

    /**
     * Get table footer.
     *
     * @return array
     */
    public function footer()
    {
        return $this->footer;
    }

    /**
     * Get raw data.
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Translate.
     *
     * @param string $key
     *
     * @return string
     */
    protected function translate($key)
    {
        return trans('plugins/log-viewer::' . $key, [], $this->locale);
    }

    /**
     * Get log level color.
     *
     * @param string $level
     *
     * @return string
     */
    protected function color($level)
    {
        return log_styler()->color($level);
    }
}
