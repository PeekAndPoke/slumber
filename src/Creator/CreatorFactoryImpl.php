<?php
/**
 * File was created 17.05.2016 06:27
 */

namespace PeekAndPoke\Component\Creator;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreatorFactoryImpl implements CreatorFactory
{
    /** @var array */
    private static $cache = [];

    /**
     * @param \ReflectionClass $class
     *
     * @return Creator
     */
    public function create(\ReflectionClass $class) : Creator
    {
        $clsName = $class->name;

        return self::$cache[$clsName] ?? self::$cache[$clsName] = $this->createInternal($class);
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return Creator
     */
    private function createInternal(\ReflectionClass $class) : Creator
    {
        $constructor = $class->getConstructor();

        // If there is no c'tor defined we can create be using default creation method.
        if ($constructor === null) {
            return new CreateWithDefaultConstructor($class);
        }

        // Is the constructor free of required parameters ?
        if ($constructor->getNumberOfRequiredParameters() === 0) {

            // If the constructor is public and has no required parameters we can use the default creation method.
            if ($constructor->isPublic()) {
                return new CreateWithDefaultConstructor($class);
            }

            // Otherwise we are using the protected/private creation method.
            return new CreateWithNonPublicConstructor($class);
        }

        // Last resort: create without the constructor
        return new CreateWithoutConstructor($class);
    }
}
