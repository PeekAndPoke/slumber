<?php
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsMap;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
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
     * @return array|\stdClass
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (! is_array($value) && ! $value instanceof \Traversable) {
            return null;
        }

        $result    = [];
        $nested    = $this->nested;
        $keepNulls = $nested->getOptions()->keepNullValuesInCollections();

        foreach ($value as $k => $v) {

            $slumbering = $nested->slumber($slumberer, $v);

            // check if we should keep nulls
            if ($slumbering !== null || $keepNulls) {
                $result[$k] = $slumbering;
            }
        }

        // when empty we return an instance of stdClass so that the json_serialize will really give us an empty object
        // of the form: {}
        return empty($result) ? new \stdClass() : $result;
    }

    /**
     * @param Awaker             $awaker
     * @param array|\Traversable $value
     *
     * @return array
     */
    public function awake(Awaker $awaker, $value)
    {
        if (! is_array($value) && ! $value instanceof \Traversable) {
            return [];
        }

        $result    = [];
        $nested    = $this->nested;
        $keepNulls = $nested->getOptions()->keepNullValuesInCollections();

        foreach ($value as $k => $v) {

            $awoken = $nested->awake($awaker, $v);

            // check if we should keep nulls
            if ($awoken !== null || $keepNulls) {
                $result[$k] = $awoken;
            }
        }

        // do we need to instantiate a collection class ?
        return $this->createAwakeResult($result);
    }
}
