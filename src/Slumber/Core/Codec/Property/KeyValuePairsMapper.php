<?php
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Collections\Collection;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsKeyValuePairs;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @method AsKeyValuePairs getOptions()
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class KeyValuePairsMapper extends AbstractCollectionMapper
{
    /**
     * @param Slumberer          $slumberer
     * @param array|\Traversable $value
     *
     * @return array|null
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (! \is_array($value) && ! $value instanceof \Traversable) {
            return null;
        }

        $result    = [];
        $nested    = $this->nested;
        $keepNulls = $nested->getOptions()->keepNullValuesInCollections();

        foreach ($value as $k => $v) {

            $slumbering = $nested->slumber($slumberer, $v);

            // check if we should keep nulls
            if ($slumbering !== null || $keepNulls) {
                $result[] = [
                    'k' => (string) $k,
                    'v' => $slumbering,
                ];
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

            [$keyToUse, $valToUse] = self::extractKv($k, $v);

            $awoken = $nested->awake($awaker, $valToUse);

            // check if we should keep nulls
            if ($keepNulls || $awoken !== null) {
                $result[$keyToUse] = $awoken;
            }
        }

        // do we need to instantiate a collection class ?
        return $this->createAwakeResult($result);
    }

    private static function extractKv($k, $v)
    {
        if (isset($v['k'], $v['v'])) {
            return [(string) $v['k'], $v['v']];
        }

        // bit of compatibility
        return [$k, $v];
    }
}
