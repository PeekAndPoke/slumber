<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 12.04.17
 * Time: 06:26
 */

namespace PeekAndPoke\Component\Slumber\Data;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface StorageDriverFactory
{
    /**
     * @param mixed            $config
     * @param string           $tableName
     * @param \ReflectionClass $baseClass
     *
     * TODO: have a typed config parameter
     *
     * @return StorageDriver
     */
    public function create($config, $tableName, \ReflectionClass $baseClass);
}
