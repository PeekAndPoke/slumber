<?php
/**
 * Created by gerk on 14.11.17 16:44
 */

namespace PeekAndPoke\Component\Toolbox\Stubs;

use Traversable;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestTraversableClass implements \IteratorAggregate
{
    /** @var array */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Retrieve an external iterator
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayObject($this->data);
    }
}
