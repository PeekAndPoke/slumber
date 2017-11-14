<?php
/**
 * Created by gerk on 14.11.17 17:51
 */

namespace PeekAndPoke\Component\Slumber\Stubs;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\Data\Addon\PublicReference\SlumberUniquelyReferenced;
use PeekAndPoke\Component\Slumber\Data\Addon\SlumberId;
use PeekAndPoke\Component\Slumber\Data\Addon\SlumberTimestamped;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 *
 * @Slumber\Store\Journalized()
 */
class UnitTestJournalizedClass
{
    use SlumberId;
    use SlumberUniquelyReferenced;
    use SlumberTimestamped;

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $name;

    /**
     * @var int
     *
     * @Slumber\AsInteger()
     */
    private $age;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     *
     * @return $this
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }
}
