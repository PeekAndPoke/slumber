<?php
/**
 * File was created 07.07.2016 06:52
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store\ListeningTo;

use PeekAndPoke\Component\Emitter\Listener;
use PeekAndPoke\Component\Psi\Psi\IsInstanceOf;
use PeekAndPoke\Component\Slumber\Annotation\ServiceInjectingSlumberAnnotation;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ClassAnnotationValidationContext;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class AbstractListenerMarker extends ServiceInjectingSlumberAnnotation
{
    /** @noinspection SenselessProxyMethodInspection */
    /**
     * Convenience method to type-hint the returned value
     *
     * @param ContainerInterface $provider
     *
     * @return Listener
     */
    protected function getService(ContainerInterface $provider)
    {
        return parent::getService($provider);
    }

    /**
     * @param ClassAnnotationValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context)
    {
        parent::validate($context);

        $service  = $this->getServiceDefinition();
        $instance = $this->getService($context->getProvider());
        $ofClass  = Listener::class;
        $check    = new IsInstanceOf($ofClass);

        if (! $check->__invoke($instance)) {
            throw $this->createValidationException(
                $context,
                "the service '$service' is not of instance '$ofClass' but is '" .
                (\is_object($instance) ? \get_class($instance) : \gettype($instance)) . "'"
            );
        }
    }
}
