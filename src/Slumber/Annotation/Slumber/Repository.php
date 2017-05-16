<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 04.01.17
 * Time: 06:28
 */
declare(strict_types=1);

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target("CLASS")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class Repository extends Annotation
{
    /**
     * @var string The name of the storage holding the repository
     */
    public $storage = 'default';

    public function getName() : string
    {
        return (string) $this->value;
    }

    /**
     * @return string
     */
    public function getStorage() : string
    {
        return $this->storage;
    }
}
