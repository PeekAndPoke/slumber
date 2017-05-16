<?php
/**
 * File was created 11.05.2016 10:13
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsCollection;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface CollectionMapper extends Mapper
{
    /**
     * @return AsCollection
     */
    public function getOptions();

    /**
     * @return Mapper
     */
    public function getNested();

    /**
     * @param Mapper $nested
     *
     * @return $this
     */
    public function setNested(Mapper $nested);

    /**
     * Traverse multiple nested Collection mappers, find the leave and check its type.
     *
     * @param string $class
     *
     * @return bool
     */
    public function isLeaveOfType($class);

    /**
     * Traverse multiple nested Collection mappers, and find the leave.
     *
     * @return Mapper
     */
    public function getLeaf();

    /**
     * Traverse multiple nested Collection mappers, and replace the leave.
     *
     * @param Mapper $leave
     */
    public function setLeaf(Mapper $leave);

    /**
     * @return null|CollectionMapper
     */
    public function getLeafParent();

    /**
     * @param string $collectionClass
     *
     * @return $this
     */
    public function setLeafParentsCollectionType($collectionClass);

    /**
     * Get the nesting level.
     *
     * If there is no nested element the level is 0.
     * If there is a nested element the level is 1.
     * If there is a nested element with a nested element as well the level 2.
     *
     * ... and so forth
     *
     * @return int
     */
    public function getNestingLevel();
}
