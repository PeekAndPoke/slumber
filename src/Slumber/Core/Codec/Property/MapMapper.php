<?php
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Collections\Collection;
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
     * @return null|\stdClass We return a std class as this will ensure json-encode will create something like {"0":1}
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (! \is_array($value) && ! $value instanceof \Traversable) {
            return null;
        }

        $result    = new \stdClass();
        $nested    = $this->nested;
        $keepNulls = $nested->getOptions()->keepNullValuesInCollections();

        foreach ($value as $k => $v) {

            $slumbering = $nested->slumber($slumberer, $v);

            // check if we should keep nulls
            if ($slumbering !== null || $keepNulls) {
                $result->$k = $slumbering;
            }
        }

        return $result;
    }

    /**
     * @param Awaker             $awaker
     * @param array|\Traversable $value
     *
     * @return array|Collection
     */
    public function awake(Awaker $awaker, $value)
    {
        if (! \is_array($value) && ! $value instanceof \Traversable) {
            return $this->createAwakeResult([]);
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
