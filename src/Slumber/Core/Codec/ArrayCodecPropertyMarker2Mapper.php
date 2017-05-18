<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 03.04.17
 * Time: 00:26
 */
declare(strict_types=1);

namespace PeekAndPoke\Component\Slumber\Core\Codec;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\Core;
use PeekAndPoke\Component\Slumber\Core\LookUp\PropertyMarker2Mapper;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ArrayCodecPropertyMarker2Mapper extends PropertyMarker2Mapper
{
    public function __construct()
    {
        parent::__construct(
            Core\Codec\Property\AsIsMapper::class,
            [
                // nested objects and collections of nested objects
                Slumber\AsObject::class             => Core\Codec\Property\ObjectMapper::class,
                Slumber\AsList::class               => Core\Codec\Property\ListMapper::class,
                Slumber\AsMap::class                => Core\Codec\Property\MapMapper::class,
                Slumber\AsKeyValuePairs::class      => Core\Codec\Property\KeyValuePairsMapper::class,
                // no mapping
                Slumber\AsIs::class                 => Core\Codec\Property\AsIsMapper::class,
                // primitive types
                Slumber\AsBool::class               => Core\Codec\Property\BoolMapper::class,
                Slumber\AsDecimal::class            => Core\Codec\Property\DecimalMapper::class,
                Slumber\AsInteger::class            => Core\Codec\Property\IntegerMapper::class,
                Slumber\AsSmallInt::class           => Core\Codec\Property\SmallIntMapper::class,
                Slumber\AsString::class             => Core\Codec\Property\StringMapper::class,
                // object and other common types
                Slumber\AsEnum::class               => Core\Codec\Property\EnumMapper::class,
                Slumber\AsSimpleDate::class         => Core\Codec\Property\SimpleDateMapper::class,
                Slumber\AsLocalDate::class          => Core\Codec\Property\LocalDateMapper::class,
                // geo json
                Slumber\GeoJson\AsPoint::class      => Core\Codec\Property\GeoJson\PointMapper::class,
                Slumber\GeoJson\AsLineString::class => Core\Codec\Property\GeoJson\LineStringMapper::class,
            ]
        );
    }
}
