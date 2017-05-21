<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 26.01.17
 * Time: 12:25
 */

namespace PeekAndPoke\Component\Slumber\FrameworkIntegration\StorageProfiler;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CompletedSample
{
    /** @var string */
    private $name;
    /** @var float */
    private $startedAt;
    /** @var float */
    private $finishedAt;
    /** @var array */
    private $data;

    /**
     * CompletedSample constructor.
     *
     * @param RunningSample $sample
     * @param float|null    $finishedAt
     */
    public function __construct(RunningSample $sample, $finishedAt = null)
    {
        $this->name       = $sample->getName();
        $this->startedAt  = $sample->getStartedAt();
        $this->finishedAt = $finishedAt ?: microtime(true);
        $this->data       = $sample->getData();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @return float
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
