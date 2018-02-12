<?php
/**
 * File was created 22.06.2016 06:01
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Types\ValueHolder;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class LazyDbReference implements ValueHolder
{
    /** @var Repository */
    private $repository;
    /** @var mixed */
    private $referencedId;
    /** @var mixed */
    private $referenced;
    /** @var bool */
    private $alreadyTriedToLoad = false;

    /**
     * @param Repository $repository
     * @param mixed      $referencedId
     *
     * @return static
     */
    public static function create(Repository $repository, $referencedId)
    {
        $result               = new static(null);
        $result->repository   = $repository;
        $result->referencedId = $referencedId;

        return $result;
    }

    /**
     * @param mixed $subject
     *
     * @return mixed
     */
    public static function unwrap($subject)
    {
        if ($subject instanceof self) {
            return $subject->getValue();
        }

        return $subject;
    }

    /**
     * LazyDbReference constructor.
     *
     * @param mixed $referenced
     */
    public function __construct($referenced)
    {
        $this->referenced = $referenced;
    }

    /**
     * @return mixed
     */
    public function getReferencedId()
    {
        return $this->referencedId;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->referenced !== null) {
            return $this->referenced;
        }

        // are we set up and did not try to load yet?
        if ($this->alreadyTriedToLoad === false) {
            // remember that we already tried to load
            $this->alreadyTriedToLoad = true;

            $this->referenced = $this->repository->findById($this->referencedId);
        }

        return $this->referenced;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws \RuntimeException always
     */
    public function __call($name, $arguments)
    {
        $value = $this->getValue();

        if ($value === null) {
            throw new \RuntimeException(
                "The referenced object was not found in repo '{$this->repository->getName()}'::'{$this->referencedId}'"
            );
        }

        $type = \is_object($value) ? \get_class($value) : \gettype($value);

        throw new \RuntimeException(
            'Tried to access a DbReference without previously unwrapping it. Call $...->getValue() first. ' .
            "The wrapped object is of type '$type' and you called the method $name()"
        );
    }
}
