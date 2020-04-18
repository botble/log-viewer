<?php

namespace Botble\LogViewer\Contracts\Utilities;

use Botble\LogViewer\Entities\Log;
use Illuminate\Contracts\Config\Repository as ConfigContract;

interface LogMenu
{

    /**
     * Set the config instance.
     *
     * @param ConfigContract $config
     *
     * @return self
     */
    public function setConfig(ConfigContract $config);

    /**
     * Set the log styler instance.
     *
     * @param LogStyler $styler
     *
     * @return self
     */
    public function setLogStyler(LogStyler $styler);

    /**
     * Make log menu.
     *
     * @param Log $log
     * @param bool $trans
     *
     * @return array
     */
    public function make(Log $log, $trans = true);
}
