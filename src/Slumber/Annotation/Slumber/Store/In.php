<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 04.01.17
 * Time: 05:51
 */

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
    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return (string) $this->value;
    }
}
