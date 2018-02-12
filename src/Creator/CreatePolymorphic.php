<?php
/**
 * File was created 17.05.2016 06:20
 */

namespace PeekAndPoke\Component\Creator;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreatePolymorphic implements Creator
{
    /** @var Creator[] */
    private $mapping;
    /** @var string */
    private $discriminator;
    /** @var Creator */
    private $defaultCreator;

    /**
     * CreatePolymorphic constructor.
     *
     * @param Creator[] $mapping        Key is defining the class, value is the creator for this class
     * @param string    $discriminator  The name of the field to look for when deciding which class to create
     * @param Creator   $defaultCreator The default creator to use when nothing is found in the mapping
     */
    public function __construct(array $mapping, $discriminator, Creator $defaultCreator)
    {
        $this->mapping        = $mapping;
        $this->discriminator  = $discriminator;
        $this->defaultCreator = $defaultCreator;
    }

    /**
     * Creates a new instance
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function create($data = null)
    {
        if (! \is_array($data) && ! $data instanceof \ArrayAccess) {
            return null;
        }

        // is the discriminator present
        if (isset($data[$this->discriminator])) {
            // get the value
            $type = $data[$this->discriminator];

            // do we know how to map this one
            if ($type !== null && isset($this->mapping[$type])) {
                return $this->mapping[$type]->create($data);
            }
        }

        // map to the default
        return $this->defaultCreator->create($data);
    }
}
