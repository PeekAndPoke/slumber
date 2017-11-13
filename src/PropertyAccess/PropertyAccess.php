<?php
/**
 * Created by gerk on 13.11.17 05:49
 */

namespace PeekAndPoke\Component\PropertyAccess;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface PropertyAccess
{
    /**
     * Get the value of the property
     *
     * @param mixed $subject The object to get the value from
     *
     * @return mixed
     */
    public function get($subject);

    /**
     * Set the value of the property
     *
     * @param mixed $subject The object to set the value to
     * @param mixed $value
     *
     * @return
     */
    public function set($subject, $value);
}
