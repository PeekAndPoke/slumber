<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 26.01.17
 * Time: 12:19
 */

namespace PeekAndPoke\Component\Slumber\FrameworkIntegration\StorageProfiler;

use PeekAndPoke\Component\Slumber\FrameworkIntegration\StorageProfiler;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class RunningSample
{
    /** @var StorageProfiler */
    private $profiler;
    /** @var string */
    private $name;
    /** @var array */
    private $data;
    /** @var float */
    private $startedAt;

    /**
     * Entry constructor.
     *
     * @param StorageProfiler $profiler
     * @param string          $name
     * @param array           $data
     * @param float|null      $startedAt
     */
    public function __construct(StorageProfiler $profiler, $name, array $data, $startedAt = null)
    {
        $this->profiler  = $profiler;
        $this->name      = $name;
        $this->data      = $data;
        $this->startedAt = $startedAt ?: microtime(true);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return float
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Stop recording
     */
    public function stop()
    {
        $this->profiler->stop($this);
    }
}
