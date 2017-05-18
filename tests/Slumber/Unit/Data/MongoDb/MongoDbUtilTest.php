<?php

namespace PeekAndPoke\Component\Slumber\Unit\Data\MongoDb;

use MongoDB\BSON\ObjectID;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbUtil;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbUtilTest extends TestCase
{
    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider ensureMongoIdTestProvider
     */
    public function testEnsureMongoId($input, $expected)
    {
        $response = MongoDbUtil::ensureMongoId($input);

        static::assertEquals($response, $expected);
    }

    /**
     * @return array
     */
    public static function ensureMongoIdTestProvider()
    {
        return [
            [null, null],
            ['NOT a MONGO id', 'NOT a MONGO id'],
            ['557755775577557755775577', new ObjectID('557755775577557755775577')],
            [new ObjectID('557755775577557755775577'), new ObjectID('557755775577557755775577')],
        ];
    }
}
