<?php
/**
 * File was created 08.10.2015 17:55
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Psi\Psi\IsInstanceOf;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ValidationContext;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ServiceInjectingSlumberAnnotation extends SlumberAnnotation
{
    /**
     * The ID of the request service
     *
     * @var string
     *
     * @Annotation\Required()
     */
    public $service;

    /**
     * The class that the service must be an instance of
     *
     * @var string
     *
     * @Annotation\Required()
     */
    public $ofClass;

    /**
     * @return string
     */
    public function getServiceDefinition()
    {
        return $this->service ?: $this->getServiceDefinitionDefault();
    }

    /**
     * @return null
     */
    public function getServiceDefinitionDefault()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getServiceClassDefinition()
    {
        return $this->ofClass ?: $this->getServiceClassDefinitionDefault();
    }

    /**
     * @return string
     */
    public function getServiceClassDefinitionDefault()
    {
        return null;
    }

    /**
     *
     * @param ValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context)
    {
        $service = $this->getServiceDefinition();
        $ofClass = $this->getServiceClassDefinition();

        if (empty($service) || empty($ofClass)) {
            throw $this->createValidationException(
                $context,
                "you must set the 'service' and the 'ofClass'"
            );
        }

        if (! $context->getProvider()->has($service)) {
            throw $this->createValidationException(
                $context,
                "you requested the non-existing service '$service'"
            );
        }

        if (! class_exists($ofClass) && ! interface_exists($ofClass)) {
            throw $this->createValidationException(
                $context,
                "you requested a service instance of the non-existing class or interface '$ofClass'"
            );
        }

        $instance = $this->getService($context->getProvider());
        $check    = new IsInstanceOf($ofClass);

        if (! $check->__invoke($instance)) {
            throw $this->createValidationException(
                $context,
                "the service '$service' is not of instance '$ofClass' but is '" .
                (\is_object($instance) ? \get_class($instance) : \gettype($instance)) . "'"
            );
        }
    }

    /**
     * @param ContainerInterface $provider
     *
     * @return mixed
     */
    protected function getService(ContainerInterface $provider)
    {
        return $provider->get(
            $this->getServiceDefinition()
        );
    }
}
