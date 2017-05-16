<?php
/**
 * Created by gerk on 13.11.16 11:21
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Collections\ArrayCollection;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class LazyDbReferenceCollection extends ArrayCollection
{
    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $item = parent::offsetGet($offset);

        if ($item instanceof LazyDbReference) {
            return $item->getValue();
        }

        return $item;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        // we need a special iterator in order to unwrap LazyDbReference instances
        return new LazyDbReferenceIterator(
            new \ArrayIterator($this->data)
        );
    }
}

/**
 * @internal
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class LazyDbReferenceIterator extends \IteratorIterator
{
    public function rewind()
    {
        parent::rewind();

        // skip all elements that cannot be found in the database
        while ($this->valid() && $this->current() === null) {
            $this->next();
        }
    }

    public function next()
    {
        // skip all elements that cannot be found in the database
        while ($this->valid()) {
            parent::next();

            if ($this->current() !== null) {
                return;
            }
        }
    }

    /**
     * @return mixed
     */
    public function current()
    {
        $item = parent::current();

        if ($item instanceof LazyDbReference) {
            return $item->getValue();
        }

        return $item;
    }
}
