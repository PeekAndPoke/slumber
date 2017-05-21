<?php
/**
 * Created by gerk on 15.11.16 13:12
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Types;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsCollection;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AbstractPropertyMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\CollectionMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Data\LazyDbReferenceCollection;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DbReferenceCollectionMapper extends AbstractPropertyMapper
{
    /** @var CollectionMapper */
    private $delegate;

    /**
     * DbReferenceCollectionMapper constructor.
     *
     * @param CollectionMapper $delegate
     */
    public function __construct(CollectionMapper $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @return AsCollection
     */
    public function getOptions()
    {
        return $this->delegate->getOptions();
    }

    /**
     * @param Slumberer $slumberer
     * @param mixed     $value
     *
     * @return mixed
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if ($value instanceof LazyDbReferenceCollection) {
            return $this->delegate->slumber($slumberer, $value->getData());
        }

        return $this->delegate->slumber($slumberer, $value);
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return mixed
     */
    public function awake(Awaker $awaker, $value)
    {
        return $this->delegate->awake($awaker, $value);
    }
}
