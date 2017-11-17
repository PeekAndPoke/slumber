<?php
/**
 * Created by gerk on 30.10.17 06:27
 */

namespace PeekAndPoke\Component\Slumber\Functional\MongoDb;

use PeekAndPoke\Component\Slumber\Data\Error\DuplicateError;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DuplicateKeysFeatureTest extends SlumberMongoDbTestBase
{
    public function setUp()
    {
        // clear the repo before every test
        self::$mainRepo->removeAll([]);
        self::$referencedRepo->removeAll([]);
    }

    public function testDuplicateInsertMustFailOnDuplicateId()
    {
        $first = new UnitTestMainClass();
        $first->setId('ID');

        self::$mainRepo->insert($first);

        $second = new UnitTestMainClass();
        $second->setId('ID');

        // Try inserting the same again and it must raise an exception.
        try {
            self::$mainRepo->insert($second);

            self::fail('Inserting a duplicate must fail');

        } catch (DuplicateError $e) {

            self::assertSame(
                self::DB_NAME . '.' . self::MAIN_COLLECTION,
                $e->getTable(),
                'The duplicate error must have the correct table'
            );

            self::assertSame(
                '_id_',
                $e->getIndex(),
                'The duplicate error must have the correct index'
            );

            self::assertSame(
                '{ : "ID" }',
                $e->getData(),
                'The duplicate error must have the correct data'
            );
        }
    }

    public function testDuplicateInsertMustFailOnDuplicateReference()
    {
        $first = new UnitTestMainClass();
        $first->setReference('REF');

        self::$mainRepo->insert($first);

        $second = new UnitTestMainClass();
        $second->setReference('REF');

        // Try inserting the same again and it must raise an exception.
        try {
            self::$mainRepo->insert($second);

            self::fail('Inserting a duplicate must fail');

        } catch (DuplicateError $e) {

            self::assertSame(
                self::DB_NAME . '.' . self::MAIN_COLLECTION,
                $e->getTable(),
                'The duplicate error must have the correct table'
            );

            self::assertSame(
                'reference_1',
                $e->getIndex(),
                'The duplicate error must have the correct index'
            );

            self::assertSame(
                '{ : "REF" }',
                $e->getData(),
                'The duplicate error must have the correct data'
            );
        }
    }
}
