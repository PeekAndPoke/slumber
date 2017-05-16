<?php
/**
 * Created by gerk on 13.11.16 10:59
 */

namespace PeekAndPoke\Component\Collections;

use PeekAndPoke\Component\Psi\Interfaces\Functions\BinaryFunctionInterface;
use PeekAndPoke\Component\Psi\Interfaces\Functions\UnaryFunctionInterface;
use PeekAndPoke\Component\Psi\Psi;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class AbstractCollection implements Collection
{
    /** @var array */
    protected $data;

    /**
     * AbstractCollection constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return Psi
     */
    public function psi()
    {
        return Psi::it($this);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param callable|UnaryFunctionInterface|BinaryFunctionInterface $predicate
     *
     * @return Collection
     */
    public function filter($predicate)
    {
        return new static(
            Psi::it($this->getIterator())
                ->filter($predicate)
                ->toArray()
        );
    }
}
