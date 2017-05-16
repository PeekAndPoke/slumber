<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 14:16
 */
declare(strict_types=1);

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
        /** @noinspection UnnecessaryParenthesesInspection */
        return (int) ($this->countProvider)($this->getOptions());
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        /** @noinspection UnnecessaryParenthesesInspection */
        return ($this->cursorProvider)($this->getOptions());
    }

    public function getOptions()
    {
        // see https://docs.mongodb.com/php-library/master/reference/method/MongoDBCollection-find/
        return [
            'sort'  => $this->sort,
            'skip'  => $this->skip,
            'limit' => $this->limit,
        ];
    }

    /**
     * @param mixed $sortBy
     *
     * @return $this|Cursor
     */
    public function sort($sortBy) : Cursor
    {
        $this->sort = $sortBy;

        return $this;
    }

    /**
     * @param int $skip
     *
     * @return $this|Cursor
     */
    public function skip($skip) : Cursor
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this|Cursor
     */
    public function limit($limit) : Cursor
    {
        $this->limit = $limit;

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
