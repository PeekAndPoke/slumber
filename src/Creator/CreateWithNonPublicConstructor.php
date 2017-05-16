<?php
/**
 * File was created 17.05.2016 06:09
 */

namespace PeekAndPoke\Component\Creator;


/**
 * Creator for instantiating classes with a private/protected constructor.
 *
 * The given class MUST have a constructor and the constructor MUST have zero required parameters.
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreateWithNonPublicConstructor extends AbstractCreator
{
    /** @var string */
    private $ctorScopeFqcn;

    /**
     * @param \ReflectionClass $class
     */
    public function __construct(\ReflectionClass $class)
    {
        parent::__construct($class);

        $this->ctorScopeFqcn = $class->getConstructor()->getDeclaringClass()->getName();
    }

    /**
     * @inheritdoc
     */
    public function create($data = null)
    {
        $fqcn = $this->fqcn;

        // we call the private/protected constructor with the scope of the class that is defining it
        $closure = \Closure::bind(
            function () use ($fqcn) {
                return new $fqcn();
            },
            null,
            $this->ctorScopeFqcn
        );

        return $closure();
    }
}
