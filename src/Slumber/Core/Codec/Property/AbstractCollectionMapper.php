<?php
/**
 * File was created 11.05.2016 10:14
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Collections\Collection;
use PeekAndPoke\Component\Psi\Psi\IsInstanceOf;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsCollection;
use PeekAndPoke\Component\Slumber\Core\Codec\Mapper;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class AbstractCollectionMapper extends AbstractPropertyMapper implements CollectionMapper
{
    /** @var Mapper */
    protected $nested;
    /** @var AsCollection */
    protected $options;

    /**
     * AbstractCollectionMapper constructor.
     *
     * @param AsCollection $options
     * @param Mapper       $nested
     */
    public function __construct(AsCollection $options, Mapper $nested)
    {
        $this->options = $options;
        $this->nested  = $nested;
    }

    /**
     * @param $subject
     *
     * @return bool
     */
    protected function isIterable($subject)
    {
        return \is_array($subject) || $subject instanceof \Traversable;
    }

    /**
     * @param mixed $result
     *
     * @return mixed
     */
    protected function createAwakeResult($result)
    {
        // do we need to instantiate a collection class ?
        $collectionCls = $this->options->collection;

        if (null === $collectionCls) {
            return $result;
        }

        return new $collectionCls($result);
    }

    /**
     * @return AsCollection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return Mapper
     */
    public function getNested()
    {
        return $this->nested;
    }

    /**
     * @param Mapper $nested
     *
     * @return $this
     */
    public function setNested(Mapper $nested)
    {
        $this->nested = $nested;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function isLeaveOfType($class)
    {
        $leave = $this->getLeaf();

        return (new IsInstanceOf($class))->__invoke($leave);
    }

    /**
     * @return Mapper
     */
    public function getLeaf()
    {
        $current = $this;

        while ($current instanceof CollectionMapper) {
            $current = $current->getNested();
        }

        return $current;
    }

    /**
     * @param Mapper $leave
     */
    public function setLeaf(Mapper $leave)
    {
        $current = $this;

        while ($current->getNested() instanceof CollectionMapper) {
            $current = $current->getNested();
        }

        $current->setNested($leave);
    }

    /**
     * @return null|CollectionMapper
     */
    public function getLeafParent()
    {
        $parent  = null;
        $current = $this;

        while ($current instanceof CollectionMapper) {
            $parent  = $current;
            $current = $current->getNested();
        }

        return $parent;

    }

    /**
     * @param string $collectionClass
     *
     * @return $this
     */
    public function setLeafParentsCollectionType($collectionClass)
    {
        if (! is_a($collectionClass, Collection::class, true)) {
            throw new \LogicException("The given class $collectionClass does not implement " . Collection::class);
        }

        $parent = $this->getLeafParent();

        if ($parent !== null) {
            $parent->getOptions()->setCollection($collectionClass);
        }

        return $this;
    }

    /**
     * Get the nesting level.
     *
     * If there is no nested element the level is 0 (this can never be the case, as the constructor ensures the nested mapper).
     * If there is a nested element the level is 1.
     * If there is a nested element with a nested element as well the level 2.
     *
     * ... and so forth
     *
     * @return mixed
     */
    public function getNestingLevel()
    {
        $nested = $this->getNested();

        // another collection ... increase the level by 1
        if ($nested instanceof CollectionMapper) {
            return 1 + $nested->getNestingLevel();
        }

        // not another collection ... level is 1
        return 1;
    }
}
