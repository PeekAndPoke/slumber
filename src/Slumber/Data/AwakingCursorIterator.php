<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 20:24
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AwakingCursorIterator extends \IteratorIterator
{
    /** @var Awaker */
    private $awaker;
    /** @var \ReflectionClass */
    private $entityClass;

    /**
     * @param \Traversable     $inner
     * @param Awaker           $awaker
     * @param \ReflectionClass $entityClass
     */
    public function __construct(\Traversable $inner, Awaker $awaker, \ReflectionClass $entityClass)
    {
        parent::__construct($inner);

        $this->awaker      = $awaker;
        $this->entityClass = $entityClass;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        $value = parent::current();

        return $this->awaker->awake($value, $this->entityClass);
    }
}
