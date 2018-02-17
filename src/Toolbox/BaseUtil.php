<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 12.04.17
 * Time: 06:51
 */

namespace PeekAndPoke\Component\Toolbox;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class BaseUtil
{
    /**
     * Use this method to soothe warning of your IDE like "unused parameter"
     *
     * @param array ...$args
     */
    public static function noop(... $args)
    {
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public static function createRandomToken($length = 10)
    {
        $result = '';

        while (\strlen($result) < $length) {
            $result .= str_replace('.', '', uniqid('', true));
        }

        return \substr($result, 0, $length);
    }

    /**
     * @param string $dir
     * @param int    $mode
     *
     * @codeCoverageIgnore
     */
    public static function ensureDirectory($dir, $mode = 0777)
    {
        $old = error_reporting(0);

        if (@is_dir($dir)) {
            return;
        }

        if (! @mkdir($dir, $mode, true) && ! @is_dir($dir)) {
            error_reporting($old);
            throw new \RuntimeException('Could not create directory');
        }

        error_reporting($old);
    }
}
