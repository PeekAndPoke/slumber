<?php
/**
 * File was created 01.03.2016 12:13
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface IndexDefinition
{
    public const ASCENDING  = 'ASC';
    public const DESCENDING = 'DESC';

    /**
     * @return boolean
     */
    public function isBackground();

    /**
     * @return boolean
     */
    public function isUnique();

    /**
     * @return boolean
     */
    public function isDropDups();

    /**
     * @return string
     */
    public function getDirection();

    /**
     * @return boolean
     */
    public function isSparse();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getExpireAfterSeconds();
}
