<?php
/**
 * File was created 08.10.2015 17:51
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ValidationContext;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class SlumberAnnotation implements SlumberMarker
{
    /**
     * Value property. Common among all derived classes.
     *
     * @var string
     */
    public $value;

    /**
     * Constructor.
     *
     * @param array $data Key-value for properties to be defined in this class.
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Error handler for unknown property accessor in Annotation class.
     *
     * @param string $name Unknown property name.
     *
     * @throws \BadMethodCallException
     */
    public function __get($name)
    {
        throw new \BadMethodCallException(
            sprintf("Unknown property '%s' on annotation '%s'.", $name, \get_class($this))
        );
    }

    /**
     * Error handler for unknown property mutator in Annotation class.
     *
     * @param string $name  Unknown property name.
     * @param mixed  $value Property value.
     *
     * @throws \BadMethodCallException
     */
    public function __set($name, $value)
    {
        throw new \BadMethodCallException(
            sprintf("Unknown property '%s' on annotation '%s'.", $name, \get_class($this))
        );
    }

    /**
     * Error handler for unknown property mutator in Annotation class.
     *
     * @param string $name Unknown property name.
     *
     * @throws \BadMethodCallException
     */
    public function __isset($name)
    {
        throw new \BadMethodCallException(
            sprintf("Unknown property '%s' on annotation '%s'.", $name, \get_class($this))
        );
    }

    /**
     * Initialize the annotation and validate the given parameters
     *
     * @param ValidationContext $context
     */
    public function validate(ValidationContext $context)
    {
        // noop
    }

    /**
     * @param ValidationContext $context
     * @param string            $msg
     *
     * @return string
     */
    protected function buildValidationMessage(ValidationContext $context, $msg)
    {
        return 'For ' . $context->getAnnotationLocation() . ' the validation of @' . static::class .
               ' failed, due to: ' . $msg . ' ';
    }

    /**
     * @param ValidationContext $context
     * @param string            $msg
     *
     * @return SlumberException
     */
    protected function createValidationException(ValidationContext $context, $msg)
    {
        return new SlumberException($this->buildValidationMessage($context, $msg));
    }
}
