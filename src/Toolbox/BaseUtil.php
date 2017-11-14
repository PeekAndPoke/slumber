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
     * @param string $dir
     * @param int    $mode
     *
     * @codeCoverageIgnore
     */
    public static function ensureDirectory($dir, $mode = 0777)
    {
        if (! @mkdir($dir, $mode, true) && ! @is_dir($dir)) {
            throw new \RuntimeException('Could not create directory');
        }
    }
}
