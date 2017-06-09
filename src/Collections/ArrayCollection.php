<?php
/**
 * Created by gerk on 13.11.16 10:53
 */

namespace PeekAndPoke\Component\Collections;


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
     * @param mixed    $subject
     * @param callable $replaceWhen
     *
     * @return ArrayCollection
     */
    public function appendOrReplace($subject, callable $replaceWhen)
    {
        foreach ($this->data as &$item) {

            if ($replaceWhen($item)) {
                $item = $subject;
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
     * @param $item
     *
     * @return $this
     */
    public function appendIfNotExists($item)
    {
        if (! $this->contains($item)) {
            $this->append($item);
        }

        return $this;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    public function contains($item)
    {
        foreach ($this as $storedItem) {
            if ($storedItem === $item) {
                return true;
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
     * @param $item
     */
    public function remove($item)
    {
        $result = [];

        foreach ($this as $storedItem) {
            if ($storedItem !== $item) {
                $result[] = $storedItem;
            }
        }

        $this->data = $result;
    }

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
