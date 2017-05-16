<?php
/**
 * Created by gerk on 13.11.16 10:52
 */

namespace PeekAndPoke\Component\Collections;

use PeekAndPoke\Component\Psi\Psi;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Collection extends \IteratorAggregate, \Countable
{
    /**
     * @return Psi
     */
    public function psi();
}
