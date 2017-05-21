<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 26.01.17
 * Time: 12:11
 */
declare(strict_types=1);

namespace PeekAndPoke\Component\Slumber\FrameworkIntegration;

use PeekAndPoke\Component\Slumber\Data\Cursor;
use PeekAndPoke\Component\Slumber\Data\StorageDriver;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ProfilingStorageDriverDecorator implements StorageDriver
{
    /** @var StorageProfiler */
    private $profiler;
    /** @var StorageDriver */
    private $driver;

    /**
     * ProfilingDriverDecorator constructor.
     *
     * @param StorageProfiler $profiler
     * @param StorageDriver   $driver
     */
    public function __construct(StorageProfiler $profiler, StorageDriver $driver)
    {
        $this->profiler = $profiler;
        $this->driver   = $driver;
    }

    /**
     * @return \ReflectionClass
     */
    public function getEntityBaseClass()
    {
        return $this->driver->getEntityBaseClass();
    }

    /**
     * @return mixed // TODO: better return type
     */
    public function buildIndexes()
    {
        return $this->profile(
            'buildIndexes',
            [],
            function () {
                return $this->driver->buildIndexes();
            }
        );
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    public function insert($item)
    {
        return $this->profile(
            'insert',
            [
                'type' => get_class($item),
            ],
            function () use ($item) {
                return $this->driver->insert($item);
            }
        );
    }

    /**
     * Insert ore update an item
     *
     * @param mixed $item
     *
     * @return mixed
     */
    public function save($item)
    {
        return $this->profile(
            'save',
            [
                'type' => get_class($item),
            ],
            function () use ($item) {
                return $this->driver->save($item);
            }
        );
    }

    /**
     * @param $item
     *
     * @return mixed
     */
    public function remove($item)
    {
        return $this->profile(
            'remove',
            [
                'type' => get_class($item),
            ],
            function () use ($item) {
                return $this->driver->remove($item);
            }
        );
    }

    /**
     * Remove all from this collection
     *
     * @return mixed
     */
    public function removeAll()
    {
        return $this->profile(
            'removeAll',
            [
            ],
            function () {
                return $this->driver->removeAll();
            }
        );
    }

    /**
     * @param $query
     *
     * @return Cursor
     */
    public function find(array $query = null)
    {
        return $this->profile(
            'find',
            [
                'query' => $query,
            ],
            function () use ($query) {
                return $this->driver->find($query);
            }
        );
    }

    /**
     * @param array|null $query
     *
     * @return mixed|null
     */
    public function findOne(array $query = null)
    {
        return $this->profile(
            'findOne',
            [
                'query' => $query,
            ],
            function () use ($query) {
                return $this->driver->findOne($query);
            }
        );
    }

    /**
     * @param string   $name
     * @param array    $data
     * @param callable $inner
     *
     * @return mixed
     */
    private function profile($name, array $data, callable $inner)
    {
        $sample = $this->profiler->start($name, $data);

        $result = $inner();

        $sample->stop();

        return $result;
    }
}
