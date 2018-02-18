<?php
/**
 * File was created 30.09.2015 10:49
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use Doctrine\Common\Annotations\Annotation;

/**
 * AsList treats the nested elements as a list.
 *
 * Keys are NOT preserved and are removed.
 *
 * The result of slumbering will look like:
 *
 * Input:
 * <code>
 *   array ( 'a' => 'A', 'b' => 'B' )
 * </code>
 *
 * Output:
 * <code>
 *   [ 'A', 'B' ]
 * </code>
 *
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsList extends AsCollection
{
}
