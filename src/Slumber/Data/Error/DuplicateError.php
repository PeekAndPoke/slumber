<?php
/**
 * Created by gerk on 30.10.17 06:25
 */

namespace PeekAndPoke\Component\Slumber\Data\Error;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DuplicateError extends StorageError
{
    /** @var string */
    private $table;
    /** @var string */
    private $index;
    /** @var string */
    private $data;

    /**
     * @param string          $message
     * @param string          $table
     * @param string          $index
     * @param string          $data
     * @param \Exception|null $previous
     */
    final public function __construct($message = '', $table, $index, $data, \Exception $previous = null)
    {
        parent::__construct($message, self::DUPLICATE_KEY, $previous);

        $this->table = $table;
        $this->index = $index;
        $this->data  = $data;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
