<?php
/**
 * File was created 11.04.2016 06:22
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Types;

use MongoDB\BSON\ObjectId;
use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AbstractPropertyMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbSlumberer;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbUtil;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PrimaryIdMapper extends AbstractPropertyMapper
{
    /** @var PropertyMappingMarker */
    private $options;

    /**
     * PrimaryIdMapper constructor.
     *
     * @param PropertyMappingMarker $options
     */
    public function __construct(PropertyMappingMarker $options)
    {
        $this->options = $options;
    }

    /**
     * @return PropertyMappingMarker
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param MongoDbSlumberer|Slumberer $slumberer
     * @param mixed                      $value
     *
     * @return mixed
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if ($value instanceof ObjectId) {
            return $value;
        }

        return MongoDbUtil::ensureMongoId($value);
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return mixed
     */
    public function awake(Awaker $awaker, $value)
    {
        return (string) $value;
    }
}
