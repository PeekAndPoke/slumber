<?php
/**
 * File was created 17.05.2016 08:34
 */

namespace PeekAndPoke\Component\Creator;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class NullCreator implements Creator
{
    /**
     * Creates a new instance
     *
     * @param mixed $data
     *
     * @return null
     */
    public function create($data = null)
    {
        return null;
    }
}
