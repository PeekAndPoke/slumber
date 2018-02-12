<?php
/**
 * File was created 07.10.2015 15:20
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfigReader;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ArrayCodec
{
    /** @var GenericSlumberer */
    private $slumberer;
    /** @var GenericAwaker */
    private $awaker;

    /**
     * @param EntityConfigReader $entityConfigReader
     */
    public function __construct(EntityConfigReader $entityConfigReader)
    {
        $this->slumberer = new GenericSlumberer($entityConfigReader);
        $this->awaker    = new GenericAwaker($entityConfigReader);
    }

    /**
     * @param mixed $subject
     *
     * @return array|mixed|null
     */
    public function slumber($subject)
    {
        if (\is_scalar($subject)) {  // todo: test this
            return $subject;
        }

        if (\is_array($subject)) {
            return $this->slumberArray($subject);
        }

        if (empty($subject)) {
            return null;
        }

        // put to sleep
        return (array) $this->slumberer->slumber($subject);
    }

    /**
     * @param mixed[] $subjects
     *
     * @return array
     */
    public function slumberArray(array $subjects)
    {
        $result = [];

        /**
         * @var string|int $key
         * @var mixed      $subject
         */
        foreach ($subjects as $key => $subject) {
            $result[$key] = $this->slumber($subject);
        }

        return $result;
    }

    /**
     * @param mixed                   $data
     * @param string|\ReflectionClass $cls
     *
     * @return mixed|null
     */
    public function awake($data, $cls)
    {
        if ($data === null || ! \is_array($data)) {
            return null;
        }

        if (! $cls instanceof \ReflectionClass) {
            $cls = new \ReflectionClass($cls);
        }

        return $this->awaker->awake($data, $cls);
    }

    /**
     * @param array                   $data
     * @param string|\ReflectionClass $cls
     *
     * @return array
     */
    public function awakeList($data, $cls)
    {
        if (! \is_array($data) && ! $data instanceof \Traversable) {
            return [];
        }

        if (! $cls instanceof \ReflectionClass) {
            $cls = new \ReflectionClass($cls);
        }

        $result = [];

        foreach ($data as $k => $v) {
            $result[$k] = $this->awake($v, $cls);
        }

        return $result;
    }
}
