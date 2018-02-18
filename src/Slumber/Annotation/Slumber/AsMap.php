<?php
/**
 * File was created 30.09.2015 10:49
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use Doctrine\Common\Annotations\Annotation;

/**
 * AsMap treats the nested elements as a key value map.
 *
 * Keys are preserved.
 *
 * The result of slumbering will look like:
 *
 * Input:
 * <code>
 *   array ( 'a' => 'A', 'b' => 'B' )
 *   array ( 'C', 'B' )
 * </code>
 *
 * Output:
 * <code>
 *   { 'a' : 'A', 'b' : 'B' }
 *   { '0' : 'C', '1' : 'D' )
 * </code>
 *
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsMap extends AsCollection
{
}
