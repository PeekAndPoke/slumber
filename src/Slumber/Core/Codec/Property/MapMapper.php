<?php
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Collections\Collection;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsMap;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @method AsMap getOptions()
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MapMapper extends AbstractCollectionMapper
{
    /**
     * @param Slumberer          $slumberer
     * @param array|\Traversable $value
     *
     * @return null|\stdClass We return a std class as this will ensure json-encode will create something like {"0":1}
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (false === $this->isIterable($value)) {
            return null;
        }

        $nested = $this->nested;

        if ($nested->getOptions()->keepNullValuesInCollections()) {
            return $this->slumberKeepNulls($slumberer, $value, $nested);
        }

        return $this->slumberFilterNulls($slumberer, $value, $nested);
    }

    /**
     * @param Awaker             $awaker
     * @param array|\Traversable $value
     *
     * @return array|Collection
     */
    public function awake(Awaker $awaker, $value)
    {
        if (false === $this->isIterable($value)) {
            return $this->createAwakeResult([]);
        }

        $nested = $this->nested;

        // TODO: we need a test that checks that an awaken map is a collection when a collection is specified
        // TODO: packing things into a collection should be a wrapper around this mapper and all other collection mappers
        return $this->createAwakeResult(
            $nested->getOptions()->keepNullValuesInCollections()
                ? $this->awakeKeepNulls($awaker, $value, $nested)
                : $this->awakeFilterNulls($awaker, $value, $nested)
        );
    }

    /**
     * @param Slumberer $slumberer
     * @param array     $value
     * @param Mapper    $nested
     *
     * @return \stdClass
     */
    private function slumberKeepNulls(Slumberer $slumberer, $value, Mapper $nested)
    {
        $result = new \stdClass();

        foreach ($value as $k => $v) {
            $result->$k = $nested->slumber($slumberer, $v);
        }

        return $result;
    }

    /**
     * @param Slumberer $slumberer
     * @param array     $value
     * @param Mapper    $nested
     *
     * @return \stdClass
     */
    private function slumberFilterNulls(Slumberer $slumberer, $value, Mapper $nested)
    {
        $result = new \stdClass();

        foreach ($value as $k => $v) {

            $slumbering = $nested->slumber($slumberer, $v);

            // check if we should keep nulls
            if ($slumbering !== null) {
                $result->$k = $slumbering;
            }
        }

        return $result;
    }

    /**
     * @param Awaker $awaker
     * @param array  $value
     * @param Mapper $nested
     *
     * @return array
     */
    private function awakeKeepNulls(Awaker $awaker, $value, Mapper $nested)
    {
        $result = [];

        foreach ($value as $k => $v) {
            $result[$k] = $nested->awake($awaker, $v);
        }

        return $result;

    }

    /**
     * @param Awaker $awaker
     * @param array  $value
     * @param Mapper $nested
     *
     * @return array
     */
    private function awakeFilterNulls(Awaker $awaker, $value, Mapper $nested)
    {
        $result = [];

        foreach ($value as $k => $v) {

            $awoken = $nested->awake($awaker, $v);

            // check if we should keep nulls
            if ($awoken !== null) {
                $result[$k] = $awoken;
            }
        }

        return $result;
    }
}
