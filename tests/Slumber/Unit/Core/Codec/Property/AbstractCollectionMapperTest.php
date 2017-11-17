<?php
/**
 * Created by gerk on 15.11.17 22:22
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property;

use PeekAndPoke\Component\Collections\ArrayCollection;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsInteger;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsIs;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsList;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsString;
use PeekAndPoke\Component\Slumber\Core\Codec\Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AbstractCollectionMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AsIsMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\IntegerMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\ListMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\StringMapper;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AbstractCollectionMapperTest extends TestCase
{
    /**
     * @param AbstractCollectionMapper $input
     * @param Mapper                   $expected
     *
     * @dataProvider provideTestGetNested
     */
    public function testGetNested(AbstractCollectionMapper $input, Mapper $expected)
    {
        $this->assertSame(
            $expected,
            $input->getNested(),
            'getNested() must work correctly'
        );
    }

    public function provideTestGetNested()
    {
        return [
            [
                new ListMapper(new AsList([]), $nested1 = new StringMapper(new AsString([]))),
                $nested1,
            ],
            [
                new ListMapper(new AsList([]), $nested2 = new IntegerMapper(new AsInteger([]))),
                $nested2,
            ],
        ];
    }

    public function testSetNested()
    {
        $subject = new ListMapper(new AsList([]), $nested1 = new StringMapper(new AsString([])));

        $subject->setNested($set = new AsIsMapper(new AsIs([])));

        $this->assertSame(
            $set,
            $subject->getNested(),
            'setNested() must work'
        );
    }

    /**
     * @param AbstractCollectionMapper $input
     * @param string                   $type
     * @param bool                     $expected
     *
     * @dataProvider provideTestIsLeafOfType
     */
    public function testIsLeafOfType($input, $type, $expected)
    {
        $this->assertSame(
            $expected,
            $input->isLeaveOfType($type),
            'isLeafOfType() must work correctly'
        );
    }

    public function provideTestIsLeafOfType()
    {
        return [
            // positive cases
            [
                new ListMapper(
                    new AsList([]),
                    new StringMapper(new AsString([]))
                ),
                StringMapper::class,
                true,
            ],
            [
                new ListMapper(
                    new AsList([]),
                    new ListMapper(
                        new AsList([]),
                        new StringMapper(new AsString([]))
                    )
                ),
                StringMapper::class,
                true,
            ],
            [
                new ListMapper(
                    new AsList([]),
                    new ListMapper(
                        new AsList([]),
                        new ListMapper(
                            new AsList([]),
                            new StringMapper(new AsString([]))
                        )
                    )
                ),
                StringMapper::class,
                true,
            ],
            // negative cases
            [
                new ListMapper(
                    new AsList([]),
                    new StringMapper(new AsString([]))
                ),
                IntegerMapper::class,
                false,
            ],
            [
                new ListMapper(
                    new AsList([]),
                    new ListMapper(
                        new AsList([]),
                        new StringMapper(new AsString([]))
                    )
                ),
                IntegerMapper::class,
                false,
            ],
            [
                new ListMapper(
                    new AsList([]),
                    new ListMapper(
                        new AsList([]),
                        new ListMapper(
                            new AsList([]),
                            new StringMapper(new AsString([]))
                        )
                    )
                ),
                IntegerMapper::class,
                false,
            ],
        ];
    }

    public function testSetLeafLevel1()
    {
        $subject = new ListMapper(new AsList([]), new StringMapper(new AsString([])));

        $subject->setLeaf($leaf = new IntegerMapper(new AsInteger([])));

        $this->assertSame($leaf, $subject->getLeaf(), 'Setting the leaf must work');
        $this->assertSame($leaf, $subject->getNested(), 'Setting the leaf must work');
        $this->assertSame(1, $subject->getNestingLevel(), 'Nesting level must be correct after setting the leaf');
    }

    public function testSetLeafLevel2()
    {
        $subject = new ListMapper(
            new AsList([]),
            new ListMapper(
                new AsList([]),
                new StringMapper(new AsString([]))
            )
        );

        $subject->setLeaf($leaf = new IntegerMapper(new AsInteger([])));

        $this->assertSame($leaf, $subject->getLeaf(), 'Setting the leaf must work');
        $this->assertSame(2, $subject->getNestingLevel(), 'Nesting level must be correct after setting the leaf');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertSame($leaf, $subject->getNested()->getNested(), 'Setting the leaf must work');
    }

    /**
     * @param AbstractCollectionMapper $input
     * @param Mapper                   $expected
     *
     * @dataProvider provideTestGetLeafParent
     */
    public function testGetLeafParent(AbstractCollectionMapper $input, Mapper $expected)
    {
        $this->assertSame(
            $expected,
            $input->getLeafParent(),
            'getLeafParent() must work'
        );
    }

    public function provideTestGetLeafParent()
    {
        return [
            [
                $leafParent = new ListMapper(
                    new AsList([]),
                    new StringMapper(new AsString([]))
                ),
                $leafParent
            ],
            [
                new ListMapper(
                    new AsList([]),
                    $leafParent = new ListMapper(
                        new AsList([]),
                        new StringMapper(new AsString([]))
                    )
                ),
                $leafParent
            ],
        ];
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetLeafParentsCollectionTypeThrows()
    {
        $subject = new ListMapper(new AsList([]), new StringMapper(new AsString([])));

        $subject->setLeafParentsCollectionType(\stdClass::class);
    }

    public function testSetLeafParentsCollectionTypeLevel1()
    {
        $subject = new ListMapper(
            new AsList([]),
            new StringMapper(new AsString([]))
        );

        $subject->setLeafParentsCollectionType(ArrayCollection::class);

        $this->assertSame(
            ArrayCollection::class,
            $subject->getOptions()->getCollection(),
            'Setting leaf parents collection type must work'
        );
    }

    public function testSetLeafParentsCollectionTypeLevel12()
    {
        $subject = new ListMapper(
            new AsList([]),
            new ListMapper(
                new AsList([]),
                new StringMapper(new AsString([]))
            )
        );

        $subject->setLeafParentsCollectionType(ArrayCollection::class);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertSame(
            ArrayCollection::class,
            $subject->getNested()->getOptions()->getCollection(),
            'Setting leaf parents collection type must work'
        );
    }
}
