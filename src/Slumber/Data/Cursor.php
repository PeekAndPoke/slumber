<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 18:44
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Psi\Psi;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Cursor extends \IteratorAggregate, \Countable
{
    /**
     * @return \Iterator
     */
    public function getIterator();

    /**
     * @param mixed $sortBy
     *
     * @return Cursor
     */
    public function sort($sortBy);

    /**
     * @param int $skip
     *
     * @return Cursor
     */
    public function skip($skip);

    /**
     * @param int $limit
     *
     * @return Cursor
     */
    public function limit($limit);

    /**
     * @return mixed
     */
    public function getFirst();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return Psi
     */
    public function psi();
}
