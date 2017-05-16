<?php
/**
 * File was created 17.05.2016 06:10
 */

namespace PeekAndPoke\Component\Creator;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Creator
{
    /**
     * Creates a new instance
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function create($data = null);
}
