<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 04.01.17
 * Time: 05:51
 */
declare(strict_types=1);

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target("CLASS")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class In extends Annotation
{
    public function getRepositoryName() : string
    {
        return (string) $this->value;
    }
}
