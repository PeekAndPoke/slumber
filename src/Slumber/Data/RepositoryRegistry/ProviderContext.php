<?php
/**
 * Created by gerk on 18.12.17 14:47
 */

namespace PeekAndPoke\Component\Slumber\Data\RepositoryRegistry;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ProviderContext
{
    /** @var string */
    private $name;
    /** @var array */
    private $classes;

    /**
     * @param string $name
     * @param array  $classes
     */
    public function __construct($name, array $classes)
    {
        $this->name    = $name;
        $this->classes = $classes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \ReflectionClass
     */
    public function getFirstClass()
    {
        return new \ReflectionClass($this->classes[0]);
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
