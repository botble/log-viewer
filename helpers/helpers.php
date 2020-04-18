<?php

if (!function_exists('log_viewer')) {
    /**
     * Get the LogViewer instance.
     *
     * @return \Botble\LogViewer\Contracts\LogViewer
     */
    function log_viewer()
    {
        return app('botble::log-viewer');
    }
}

if (!function_exists('log_levels')) {
    /**
     * Get the LogLevels instance.
     *
     * @return \Botble\LogViewer\Contracts\Utilities\LogLevels
     */
    function log_levels()
    {
        return app('botble::log-viewer.levels');
    }
}

if (!function_exists('log_menu')) {
    /**
     * Get the LogMenu instance.
     *
     * @return \Botble\LogViewer\Contracts\Utilities\LogMenu
     */
    function log_menu()
    {
        return app('botble::log-viewer.menu');
    }
}

if (!function_exists('log_styler')) {
    /**
     * Get the LogStyler instance.
     *
     * @return \Botble\LogViewer\Contracts\Utilities\LogStyler
     */
    function log_styler()
    {
        return app('botble::log-viewer.styler');
    }
}

if (!function_exists('extract_date')) {
    /**
     * Extract date from string (format : YYYY-MM-DD).
     *
     * @param  string $string
     * @return string
     */
    function extract_date($string)
    {
        return preg_replace(
            '/.*(' . REGEX_DATE_PATTERN . ').*/',
            '$1',
            $string
        );
    }
}
