<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 26.01.17
 * Time: 12:25
 */
declare(strict_types=1);

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

    public function __construct(RunningSample $sample, float $finishedAt = null)
    {
        $this->name       = $sample->getName();
        $this->startedAt  = $sample->getStartedAt();
        $this->finishedAt = $finishedAt ?? microtime(true);
        $this->data       = $sample->getData();
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getStartedAt() : float
    {
        return $this->startedAt;
    }

    public function getFinishedAt() : float
    {
        return $this->finishedAt;
    }

    public function getData() : array
    {
        return $this->data;
    }
}
