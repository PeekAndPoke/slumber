<?php
/**
 * Created by gerk on 28.07.16 17:36
 */

namespace PeekAndPoke\Component\Slumber\Data\Migration;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class AbstractMigration implements Migration
{

    /**
     * @return string
     */
    public function getName()
    {
        $reflect = new \ReflectionClass($this);

        return $reflect->getShortName();
    }
}
