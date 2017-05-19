<?php
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Collections\Collection;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsList;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @method AsList getOptions()
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ListMapper extends AbstractCollectionMapper
{
    /**
     * @param Slumberer          $slumberer
     * @param array|\Traversable $value
     *
     * @return array
     */
    public function slumber(Slumberer $slumberer, $value) : ?array
    {
        if (! is_array($value) && ! $value instanceof \Traversable) {
            return null;
        }

        $result    = [];
        $nested    = $this->nested;
        $keepNulls = $nested->getOptions()->keepNullValuesInCollections();

        foreach ($value as $v) {

            $slumbering = $nested->slumber($slumberer, $v);

            // check if we should keep nulls
            if ($slumbering !== null || $keepNulls) {
                $result[] = $slumbering;
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
        // can we handle the input ?
        if (! is_array($value) && ! $value instanceof \Traversable) {
            return $this->createAwakeResult([]);
        }

        // handle the input
        $result    = [];
        $nested    = $this->nested;
        $keepNulls = $nested->getOptions()->keepNullValuesInCollections();

        foreach ($value as $v) {

            $awoken = $nested->awake($awaker, $v);

            // check if we should keep nulls
            if ($awoken !== null || $keepNulls) {
                $result[] = $awoken;
            }
        }

        // do we need to instantiate a collection class ?
        return $this->createAwakeResult($result);
    }
}
