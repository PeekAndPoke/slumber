<?php
/**
 * File was created 10.05.2016 17:13
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Types;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\Store\AsDbReference;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AbstractPropertyMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Data\LazyDbReference;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbAwaker;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbSlumberer;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbUtil;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DbReferenceMapper extends AbstractPropertyMapper
{
    /** @var AsDbReference */
    private $options;

    /**
     * DbReferenceMapper constructor.
     *
     * @param AsDbReference $options
     */
    public function __construct(AsDbReference $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsDbReference
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
     * @throws SlumberException
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        /**
         * unwrap any LazyDbReference
         * @see LazyDbReference
         */
        if ($value instanceof LazyDbReference) {
            // take the raw id WITHOUT reloading the object
            $id = $value->getReferencedId();

        } else {

            if (! \is_object($value)) {
                return null;
            }

            // TODO: we need a better way to ensure that getId() is available on the object
            // -> ask the repo and use the idMarker for this
            if (! method_exists($value, 'getId')) {
                throw new SlumberException('The referenced entity ' . \get_class($value) . ' must have a method called getId()');
            }

            // we do nothing. we only want the id of the referenced object
            $id = $value->getId();
        }

        return MongoDbUtil::ensureMongoId($id);
    }

    /**
     * @param MongoDbAwaker|Awaker $awaker
     * @param mixed                $value
     *
     * @return mixed
     * @throws SlumberException
     */
    public function awake(Awaker $awaker, $value)
    {
        if ($value === null) {
            return null;
        }

        $clz  = $this->getReferencedClass();
        $repo = $awaker->getStorage()->getRepositoryByClassName($clz);

        if ($repo !== null) {
            return LazyDbReference::create($repo, $value);
        }

        return null;
    }

    /**
     * @return string
     */
    private function getReferencedClass()
    {
        return $this->options->getObjectOptions()->value;
    }
}
