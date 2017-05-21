<?php

namespace PeekAndPoke\Component\Toolbox;

use Psr\Log\LoggerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ExceptionUtil
{
    /**
     * @param \Exception $e
     *
     * @return string
     */
    public static function toString(\Exception $e)
    {
        // use internal exception printing
        ob_start();
        echo (string) $e;

        return ob_get_clean();
    }

    /**
     * @param \Exception $e
     *
     * @return string[]
     */
    public static function toLines(\Exception $e)
    {
        return explode("\n", self::toString($e));
    }

    /**
     * @param LoggerInterface $logger
     * @param \Exception      $e
     * @param string          $prefix
     * @param array           $extra
     */
    public static function log(LoggerInterface $logger, \Exception $e, $prefix, array $extra = [])
    {
        $result = static::toString($e);

        $extra['exception'] = $result;

        if ($e instanceof \ErrorException) {
            $logger->alert($prefix . ' ' . static::formatMessage($e), $extra);
        } else {
            $logger->error($prefix . ' ' . static::formatMessage($e), $extra);
        }
    }

    /**
     * @param LoggerInterface $logger
     * @param \Exception      $e
     * @param string          $prefix
     * @param array           $extra
     */
    public static function warn(LoggerInterface $logger, \Exception $e, $prefix, array $extra = [])
    {
        $result = static::toString($e);

        $extra['exception'] = $result;

        $logger->warning($prefix . ' ' . static::formatMessage($e), $extra);
    }

    /**
     * @param LoggerInterface $logger
     * @param \Exception      $e
     * @param string          $prefix
     * @param array           $extra
     */
    public static function info(LoggerInterface $logger, \Exception $e, $prefix, array $extra = [])
    {
        $result = static::toString($e);

        $extra['exception'] = $result;

        $logger->info($prefix . ' ' . static::formatMessage($e), $extra);
    }

    /**
     * @param \Exception $e
     *
     * @return array
     */
    public static function toRaw(\Exception $e)
    {
        return [
            'raw' => explode(PHP_EOL, static::toString($e)),
        ];
    }

    /**
     * @param \Exception $e
     *
     * @return array
     */
    public static function toReducedArray(\Exception $e)
    {
        $originalException = $e;

        $renderedTrace = [];

        while ($e) {

            $trace                = $e->getTrace();
            $currentRenderedTrace = [];

            $idx = 0;

            foreach ($trace as $traceItem) {

                $currentRenderedTrace['#' . $idx++] =
                    (isset($traceItem['file']) ? $traceItem['file'] : '') . '(' .
                    (isset($traceItem['line']) ? $traceItem['line'] : '') . ') ' .
                    (isset($traceItem['class']) ? $traceItem['class'] : '') .
                    (isset($traceItem['type']) ? $traceItem['type'] : '') .
                    (isset($traceItem['function']) ? $traceItem['function'] : '');
            }

            $renderedTrace[] = [
                'ex'      => get_class($e),
                'message' => static::formatMessage($e),
                'trace'   => $currentRenderedTrace,
            ];

            $e = $e->getPrevious();
        }

        $json = [
            'ex'      => get_class($originalException),
            'message' => static::formatMessage($originalException),
            'trace'   => $renderedTrace,
        ];

        return $json;
    }

    /**
     * @param \Exception $e
     *
     * @return string
     */
    public static function formatMessage(\Exception $e)
    {
        return $e->getMessage() . ' (Code ' . $e->getCode() . ') at ' . $e->getFile() . ':' . $e->getLine();
    }
}
