<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 14:16
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Psi\Psi;
use PeekAndPoke\Component\Slumber\Data\Cursor;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbCursor implements Cursor
{
    /** @var mixed */
    private $sort;
    /** @var int */
    private $skip;
    /** @var int */
    private $limit;

    /** @var callable */
    private $cursorProvider;
    /** @var callable */
    private $countProvider;

    public function __construct(callable $cursorProvider, callable $countProvider)
    {
        $this->cursorProvider = $cursorProvider;
        $this->countProvider  = $countProvider;
    }

    /**
     * @return int
     */
    public function count()
    {
        return MongoDbGuard::guard(function () {

            $countProvider = $this->countProvider;

            return (int) $countProvider($this->getOptions());
        });
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return MongoDbGuard::guard(function () {

            $cursorProvider = $this->cursorProvider;

            return $cursorProvider ($this->getOptions());
        });
    }

    public function getOptions()
    {
        // see https://docs.mongodb.com/php-library/master/reference/method/MongoDBCollection-find/
        /** @noinspection UnnecessaryCastingInspection */
        return [
            'sort'  => $this->sort,
            'skip'  => (int) $this->skip,
            'limit' => (int) $this->limit,
        ];
    }

    /**
     * @param mixed $sortBy
     *
     * @return $this|Cursor
     */
    public function sort($sortBy)
    {
        $this->sort = $sortBy;

        return $this;
    }

    /**
     * @param int $skip
     *
     * @return $this|Cursor
     */
    public function skip($skip)
    {
        $this->skip = (int) $skip;

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this|Cursor
     */
    public function limit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        /** @noinspection LoopWhichDoesNotLoopInspection */
        foreach ($this->getIterator() as $next) {
            return $next;
        }

        return null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this as $item) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @return Psi
     */
    public function psi()
    {
        return Psi::it($this);
    }
}
