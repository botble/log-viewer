<?php

namespace Botble\LogViewer\Utilities;

use Botble\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Illuminate\Translation\Translator;
use Psr\Log\LogLevel;
use ReflectionClass;

class LogLevels implements LogLevelsContract
{
    /**
     * The log levels.
     *
     * @var array
     */
    protected static $levels = [];

    /**
     * The Translator instance.
     *
     * @var Translator
     */
    protected $translator;

    /**
     * The selected locale.
     *
     * @var string
     */
    protected $locale;

    /**
     * LogLevels constructor.
     *
     * @param Translator $translator
     * @param string $locale
     */
    public function __construct(Translator $translator, $locale)
    {
        $this->setTranslator($translator);
        $this->setLocale($locale);
    }

    /**
     * Set the Translator instance.
     *
     * @param Translator $translator
     *
     * @return LogLevels
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Get the selected locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale === 'auto'
            ? $this->translator->getLocale()
            : $this->locale;
    }

    /**
     * Set the selected locale.
     *
     * @param string $locale
     *
     * @return LogLevels
     */
    public function setLocale($locale)
    {
        $this->locale = empty($locale) ? 'auto' : $locale;

        return $this;
    }

    /**
     * Get the log levels.
     *
     * @param bool $flip
     *
     * @return array
     */
    public function lists($flip = false)
    {
        return self::all($flip);
    }

    /**
     * Get translated levels.
     *
     * @param string|null $locale
     *
     * @return array
     */
    public function names($locale = null)
    {
        $levels = self::all(true);

        array_walk($levels, function (&$name, $level) use ($locale) {
            $name = $this->get($level, $locale);
        });

        return $levels;
    }

    /**
     * Get PSR log levels.
     *
     * @param bool $flip
     *
     * @return array
     */
    public static function all($flip = false)
    {
        if (empty(self::$levels)) {
            self::$levels = (new ReflectionClass(LogLevel::class))
                ->getConstants();
        }

        return $flip ? array_flip(self::$levels) : self::$levels;
    }

    /**
     * Get the translated level.
     *
     * @param string $key
     * @param string|null $locale
     *
     * @return string
     */
    public function get($key, $locale = null)
    {
        if (empty($locale) || $locale === 'auto') {
            $locale = $this->getLocale();
        }

        return $this->translator->get('plugins/log-viewer::levels.' . $key, [], $locale);
    }
}
