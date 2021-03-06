<?php
/**
 * File was created 17.05.2016 06:09
 */

namespace PeekAndPoke\Component\Creator;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreateWithoutConstructor extends AbstractCreator
{
    /**
     * @inheritdoc
     */
    public function create($data = null)
    {
        return $this->getClass()->newInstanceWithoutConstructor();
    }
}
