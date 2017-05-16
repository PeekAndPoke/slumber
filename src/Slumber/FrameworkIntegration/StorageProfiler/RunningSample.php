<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 26.01.17
 * Time: 12:19
 */
declare(strict_types=1);

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
    public function __construct(StorageProfiler $profiler, string $name, array $data, float $startedAt = null)
    {
        $this->profiler  = $profiler;
        $this->name      = $name;
        $this->data      = $data;
        $this->startedAt = $startedAt ?? microtime(true);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function getStartedAt() : float
    {
        return $this->startedAt;
    }

    public function stop()
    {
        $this->profiler->stop($this);
    }
}
