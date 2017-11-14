<?php
/**
 * Created by gerk on 13.11.16 10:53
 */

namespace PeekAndPoke\Component\Collections;

use PeekAndPoke\Component\Psi\Interfaces\Functions\BinaryFunctionInterface;
use PeekAndPoke\Component\Psi\Interfaces\Functions\UnaryFunctionInterface;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ArrayCollection extends AbstractCollection implements \ArrayAccess
{
    /**
     * @param $item
     *
     * @return $this
     */
    public function append($item)
    {
        $this->data[] = $item;

        return $this;
    }

    /**
     * Add or replace by condition
     *
     * The first item that meets the condition is replaced.
     * When the condition is not met the subject will be added to the end.
     *
     * @param mixed                           $subject     The subject to append or replace with
     * @param UnaryFunctionInterface|callable $replaceWhen The condition to check (gets each entry passed in individually)
     *
     * @return ArrayCollection
     */
    public function appendOrReplace($subject, callable $replaceWhen)
    {
        foreach ($this as $k => $item) {

            if ($replaceWhen($item)) {
                $this[$k] = $subject;

                return $this;
            }
        }

        return $this->append($subject);
    }

    /**
     * @param array|\Traversable $items
     *
     * @return $this
     */
    public function appendAll($items)
    {
        if (! is_array($items) && ! $items instanceof \Traversable) {
            return $this;
        }

        foreach ($items as $item) {
            $this->append($item);
        }

        return $this;
    }

    /**
     * Append an item if it does not yet exist in the collection.
     *
     * @see contains()
     *
     * @param mixed                                 $item
     * @param BinaryFunctionInterface|callable|null $comparator
     *
     * @return $this
     */
    public function appendIfNotExists($item, $comparator = null)
    {
        if (! $this->contains($item, $comparator)) {
            $this->append($item);
        }

        return $this;
    }

    /**
     * Check if an item is in the list
     *
     * By default type safe comparison is used.
     *
     * You can provide $comparator for customer comparison.
     *
     * @param mixed                                 $item
     * @param BinaryFunctionInterface|callable|null $comparator
     *
     * @return bool
     */
    public function contains($item, $comparator = null)
    {
        if ($comparator === null) {

            foreach ($this as $storedItem) {
                if ($storedItem === $item) {
                    return true;
                }
            }

        } else {

            foreach ($this as $storedItem) {
                if ($comparator($storedItem, $item)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return mixed|null
     */
    public function getFirst()
    {
        return $this->count() > 0 ? $this[0] : null;
    }

    /**
     * @return mixed|null
     */
    public function getLast()
    {
        return $this->count() > 0 ? $this[$this->count() - 1] : null;
    }

    /**
     * Remove items by type safe comparison
     *
     * @see removeWhen()
     *
     * @param $item
     */
    public function remove($item)
    {
        return $this->removeWhen(
            function ($storedItem) use ($item) {
                return $storedItem === $item;
            }
        );
    }

    /**
     * Remove all items that meet the condition
     *
     * @param callable $removeWhen
     */
    public function removeWhen(callable $removeWhen)
    {
        $result = [];

        foreach ($this as $storedItem) {
            if (false === (bool) $removeWhen($storedItem)) {
                $result[] = $storedItem;
            }
        }

        $this->data = $result;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset An offset to check for.
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     */
    public function offsetSet($offset, $value)
    {
        if ($offset !== null) {
            // insert with key: $collection['key'] = $value
            // insert key 0:    $collection[0]     = $value;
            $this->data[$offset] = $value;
        } else {
            // append to end: $collection[] = $value
            $this->append($value);
        }
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
