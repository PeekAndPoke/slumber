<?php
/**
 * File was created 10.05.2016 17:02
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;
use PeekAndPoke\Component\Slumber\Annotation\PropertyStorageMarker;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsObject;
use PeekAndPoke\Component\Slumber\Annotation\SlumberAnnotation;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ValidationContext;

/**
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsDbReference extends SlumberAnnotation implements PropertyStorageMarker, PropertyMappingMarker
{
    /**
     * Set this to true if the reference should be handled as a lazy reference.
     *
     * TODO: Fully remove the lazy switch since lazy is the default and only behaviour
     *
     * @var bool
     */
    public $lazy = true;

    /** @var AsObject */
    private $objectOptions;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return AsObject
     */
    public function getObjectOptions()
    {
        return $this->objectOptions;
    }

    /**
     * @param AsObject $objectOptions
     *
     * @return $this
     */
    public function setObjectOptions(AsObject $objectOptions)
    {
        $this->objectOptions = $objectOptions;

        return $this;
    }

    /**
     * Initialize the annotation and validate the given parameters
     *
     * @param ValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context)
    {
        if ($this->lazy !== true) {
            throw $this->createValidationException(
                $context,
                'Only lazy db references are supported at the moment. You must specify lazy=true'
            );
        }
    }

    ////  Delegate to the object options  //////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->objectOptions->getAlias();
    }

    /**
     * @return bool
     */
    public function hasAlias()
    {
        return $this->objectOptions->hasAlias();
    }

    /**
     * @return bool
     */
    public function keepNullValuesInCollections()
    {
        return $this->objectOptions->keepNullValuesInCollections();
    }
}
