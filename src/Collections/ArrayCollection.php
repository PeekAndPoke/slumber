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
     * @return \ArrayIterator
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
        return $this->data[$offset] ?? null;
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
