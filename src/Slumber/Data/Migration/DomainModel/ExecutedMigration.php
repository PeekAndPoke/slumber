<?php
/**
 * File was created 05.07.2016 16:37
 */

namespace PeekAndPoke\Component\Slumber\Data\Migration\DomainModel;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\Data\Addon\SlumberId;
use PeekAndPoke\Component\Slumber\Data\Addon\SlumberTimestamped;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ExecutedMigration
{
    use SlumberId;
    use SlumberTimestamped;

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $name;

    /**
     * @var string[]
     *
     * @Slumber\AsList(@Slumber\AsString())
     */
    private $logs = [];

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
     * @return \string[]
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param string $log
     *
     * @return $this
     */
    public function appendLog($log)
    {
        $this->logs[] = $log;

        return $this;
    }
}
