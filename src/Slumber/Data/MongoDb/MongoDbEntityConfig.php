<?php
/**
 * File was created 11.02.2016 17:38
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Psi\Functions\Unary\Matcher\IsInstanceOf;
use PeekAndPoke\Component\Psi\Psi;
use PeekAndPoke\Component\Slumber\Annotation\ClassMarker;
use PeekAndPoke\Component\Slumber\Annotation\ClassPostDeleteListenerMarker;
use PeekAndPoke\Component\Slumber\Annotation\ClassPostSaveListenerMarker;
use PeekAndPoke\Component\Slumber\Annotation\CompoundIndexDefinition;
use PeekAndPoke\Component\Slumber\Annotation\PropertyPreSaveVisitorMarker;
use PeekAndPoke\Component\Slumber\Annotation\PropertyStorageIndexMarker;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsObject;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\Store\AsDbReference;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\Store\AsId;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\CollectionMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\ObjectMapper;
use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfig;
use PeekAndPoke\Component\Slumber\Core\LookUp\PropertyMarkedForIndexing;
use PeekAndPoke\Component\Slumber\Core\LookUp\PropertyMarkedForPreSaveVisit;
use PeekAndPoke\Component\Slumber\Core\LookUp\PropertyMarkedForSlumber;
use PeekAndPoke\Component\Slumber\Data\LazyDbReferenceCollection;
use PeekAndPoke\Component\Slumber\Data\MongoDb\Types\DbReferenceCollectionMapper;
use PeekAndPoke\Component\Slumber\Data\MongoDb\Types\DbReferenceMapper;
use PeekAndPoke\Component\Slumber\Data\MongoDb\Types\PrimaryIdMapper;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbEntityConfig extends EntityConfig
{
    /** @var PropertyMarkedForSlumber */
    protected $idMarker;

    /** @var array|PropertyMarkedForPreSaveVisit[] */
    protected $preSaveVisits = [];

    /** @var array|ClassPostSaveListenerMarker[] */
    protected $postSaveClassListeners = [];
    /** @var array|ClassPostDeleteListenerMarker[] */
    protected $postDeleteClassListeners = [];

    /** @var array|PropertyMarkedForIndexing[] */
    protected $indexedProperties = [];
    /** @var array|CompoundIndexDefinition[] */
    protected $compoundIndexes = [];

    /**
     * @param EntityConfig $config
     *
     * @return MongoDbEntityConfig
     */
    public static function from(EntityConfig $config)
    {
        $result = new MongoDbEntityConfig(
            $config->getClassName(),
            $config->getCreator(),
            $config->getMarkersOnClass(),
            $config->getMarkedProperties()
        );

        $result->initialize();

        return $result;
    }

    /**
     *
     * TODO: We need PropertyAccess implementation and do $access = $entityConfig->getIdAccess(); $access->get()
     *
     * Why do we need this? Because the id could be defined as private in a base class. This issue is all over the place ...
     * So where ever getIdMarker is used, it is most likely wrong
     *
     * @return PropertyMarkedForSlumber
     */
    public function getIdMarker()
    {
        return $this->idMarker;
    }

    /**
     * @return CompoundIndexDefinition[]
     */
    public function getCompoundIndexes()
    {
        return $this->compoundIndexes;
    }

    /**
     * @return PropertyMarkedForIndexing[]
     */
    public function getIndexedProperties()
    {
        return $this->indexedProperties;
    }

    /**
     * @return PropertyMarkedForPreSaveVisit[]
     */
    public function getPreSaveVisits()
    {
        return $this->preSaveVisits;
    }

    /**
     * @return bool
     */
    public function hasPostSaveClassListeners()
    {
        return count($this->postSaveClassListeners) > 0;
    }

    /**
     * @return ClassPostSaveListenerMarker[]
     */
    public function getPostSaveClassListeners()
    {
        return $this->postSaveClassListeners;
    }

    /**
     * @return bool
     */
    public function hasPostDeleteClassListeners()
    {
        return count($this->postDeleteClassListeners) > 0;
    }

    /**
     * @return ClassPostDeleteListenerMarker[]
     */
    public function getPostDeleteClassListeners()
    {
        return $this->postDeleteClassListeners;
    }

    private function initialize()
    {
        // setup and modify the id-marker
        $this->setUpIdMarker();

        // setup and modify markers with database-relations
        $this->setUpDbReferenceMarkers();

        // setup property live-cycle event listeners
        $this->setUpPreSaveVisits();

        // setup class live-cycle event listeners
        $this->setUpPostSaveClassListeners();
        $this->setUpPostDeleteClassListeners();

        // setup indexes
        $this->setUpIndexedProperties();
        $this->setUpCompoundIndexes();
    }

    private function setUpIdMarker()
    {
        // we modify the property markers if we find an AsId::class

        $this->markedProperties = Psi::it($this->getMarkedProperties())
            ->filter(new IsInstanceOf(PropertyMarkedForSlumber::class))
            ->map(function (PropertyMarkedForSlumber $p) {

                // modify AsId markers
                if ($this->idMarker === null && $p->getFirstMarkerOf(AsId::class)) {
                    // remember the property marked as primary id
                    $this->idMarker = PropertyMarkedForSlumber::create(
                        $p->name,
                        '_id',
                        $p->marker,
                        $p->allMarkers,
                        new PrimaryIdMapper($p->mapper->getOptions())
                    );

                    return $this->idMarker;
                }

                // return unmodified
                return $p;
            })
            ->toArray();
    }

    private function setUpDbReferenceMarkers()
    {
        $this->markedProperties = Psi::it($this->getMarkedProperties())
            ->filter(new IsInstanceOf(PropertyMarkedForSlumber::class))
            ->map(function (PropertyMarkedForSlumber $p) {

                /** @var AsDbReference $asDbRef */
                $asDbRef = $p->getFirstMarkerOf(AsDbReference::class);

                // do we have a AsDbReference marker ?
                if ($asDbRef) {

                    // is it accompanied by an AsObject marker ?

                    /** @var AsObject $asObj */
                    $asObj = $p->getFirstMarkerOf(AsObject::class);

                    if ($asObj) {
                        // we need the actual object
                        $asDbRef->setObjectOptions($asObj);

                        // return the modified marker
                        return PropertyMarkedForSlumber::create(
                            $p->name,
                            $p->alias,
                            $p->marker,
                            $p->allMarkers,
                            new DbReferenceMapper($asDbRef)
                        );
                    }

                    // is it a collection and has an ObjectMapper as leave? Then we replace t
                    if ($p->mapper instanceof CollectionMapper && $p->mapper->isLeaveOfType(ObjectMapper::class)) {

                        /** @var ObjectMapper $leaf */
                        $leaf = $p->mapper->getLeaf();
                        // set the options of the referenced object
                        $asDbRef->setObjectOptions($leaf->getOptions());
                        // replace the leave mapper
                        $p->mapper->setLeaf(new DbReferenceMapper($asDbRef));

                        // we must also wrap the out collection so that we can un-wrap the LazyDbReferences
                        if ($asDbRef->lazy) {
                            $p->mapper->setLeafParentsCollectionType(LazyDbReferenceCollection::class);
                        }

                        // when the collection only has one child (Any Slumber\As...) we have to replace the mapper itself
                        if ($p->mapper->getNestingLevel() === 1) {
                            /** @noinspection PhpParamsInspection */
                            $p->mapper = new DbReferenceCollectionMapper($p->mapper);
                        }
                        // else {
                        // TODO: to be implemented . We need to replace the mapper of the leafs grand-parent, just like above
                        //       it works without it but has a high performance impact
                        // }

                        return $p;
                    }
                }

                // return un-modified
                return $p;
            })
            ->toArray();
    }

    private function setUpPreSaveVisits()
    {
        // set up the pre save property visits
        $this->preSaveVisits = array_merge(
            $this->preSaveVisits,
            Psi::it($this->getMarkedProperties())
                ->filter(new IsInstanceOf(PropertyMarkedForSlumber::class))
                ->filter(function (PropertyMarkedForSlumber $p) {
                    return $p->getFirstMarkerOf(PropertyPreSaveVisitorMarker::class) !== null;
                })
                ->map(function (PropertyMarkedForSlumber $p) {
                    $res               = new PropertyMarkedForPreSaveVisit();
                    $res->propertyName = $p->name;
                    $res->markers      = $p->getMarkersOf(PropertyPreSaveVisitorMarker::class);

                    return $res;
                })
                ->toArray()
        );
    }

    private function setUpPostSaveClassListeners()
    {
        $this->postSaveClassListeners = array_merge(
            $this->postSaveClassListeners,
            Psi::it($this->getMarkersOnClass())
                ->filter(new IsInstanceOf(ClassPostSaveListenerMarker::class))
                ->toArray()
        );
    }

    private function setUpPostDeleteClassListeners()
    {
        $this->postDeleteClassListeners = array_merge(
            $this->postDeleteClassListeners,
            Psi::it($this->getMarkersOnClass())
                ->filter(new IsInstanceOf(ClassPostDeleteListenerMarker::class))
                ->toArray()
        );
    }

    private function setUpIndexedProperties()
    {
        // get indexed properties
        // TODO: write tests
        $this->indexedProperties = array_merge(
            $this->indexedProperties,
            Psi::it($this->getMarkedProperties())
                ->filter(new IsInstanceOf(PropertyMarkedForSlumber::class))
                ->filter(function (PropertyMarkedForSlumber $p) {
                    return $p->getFirstMarkerOf(PropertyStorageIndexMarker::class) !== null;
                })
                ->map(function (PropertyMarkedForSlumber $p) {
                    $res               = new PropertyMarkedForIndexing();
                    $res->propertyName = $p->name;
                    $res->markers      = $p->getMarkersOf(PropertyStorageIndexMarker::class);

                    return $res;
                })
                ->toArray()
        );
    }

    private function setUpCompoundIndexes()
    {
        // get compound indexes
        // TODO: write tests
        $this->compoundIndexes = array_merge(
            $this->compoundIndexes,
            Psi::it($this->getMarkersOnClass())
                ->filter(new IsInstanceOf(ClassMarker::class))
                ->filter(new IsInstanceOf(CompoundIndexDefinition::class))
                ->toArray()
        );
    }
}
