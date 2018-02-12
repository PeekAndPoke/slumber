<?php
/**
 * File was created 14.05.2016 22:32
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Creator\CreatePolymorphic;
use PeekAndPoke\Component\Creator\CreatorFactory;
use PeekAndPoke\Component\Creator\NullCreator;
use PeekAndPoke\Component\Slumber\Annotation\ClassCreatorMarker;
use PeekAndPoke\Component\Slumber\Annotation\SlumberAnnotation;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ValidationContext;


/**
 * @Annotation
 * @Annotation\Target("CLASS")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class Polymorphic extends SlumberAnnotation implements ClassCreatorMarker
{
    public const DEFAULT_TELL_BY = 'type';

    /**
     * The data field to look at in order to tell which kind of object to instantiate
     *
     * @var string
     *
     * @Annotation\Attribute(required=false)
     */
    public $tellBy = self::DEFAULT_TELL_BY;

    /**
     * The default type to use, when no nothing was found in the mapping.
     *
     * Can be left empty, then "null" will be result for a type that could not be mapped.
     *
     * @var string
     *
     * @Annotation\Attribute(required=false)
     */
    public $default;

    /**
     * Initialize the annotation and validate the given parameters
     *
     * @param ValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context)
    {
        $mapping = $this->getMapping();

        if (! \is_array($mapping)) {
            throw $this->createValidationException($context, 'The type-mapping is missing');
        }

        foreach ($mapping as $k => $v) {
            if (! class_exists($v)) {
                throw $this->createValidationException(
                    $context,
                    'The class "' . $v . '" for "' . $k . '"does not exist'
                );
            }
        }

        if (! empty($this->default) && ! class_exists($this->default)) {
            throw $this->createValidationException(
                $context,
                'The class "' . $this->default . ' for the default does not exist'
            );
        }
    }

    /**
     * @return string
     */
    public function getTellBy()
    {
        return $this->tellBy ?: self::DEFAULT_TELL_BY;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return (array) $this->value;
    }

    /**
     * @inheritdoc
     */
    public function getCreator(CreatorFactory $factory)
    {
        $mapping = [];

        foreach ($this->getMapping() as $k => $v) {
            $mapping[$k] = $factory->create(new \ReflectionClass($v));
        }

        return new CreatePolymorphic(
            $mapping,
            $this->getTellBy(),
            ! empty($this->default) ? $factory->create(new \ReflectionClass($this->default)) : new NullCreator()
        );
    }
}
