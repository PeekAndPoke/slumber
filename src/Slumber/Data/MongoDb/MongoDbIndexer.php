<?php
/**
 * File was created 29.02.2016 17:08
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use MongoDB\Collection;
use MongoDB\Driver\Exception;
use MongoDB\Model\IndexInfo;
use PeekAndPoke\Component\Psi\Functions\Unary\Matcher\IsInstanceOf;
use PeekAndPoke\Component\Psi\Psi;
use PeekAndPoke\Component\Slumber\Annotation\CompoundIndexDefinition;
use PeekAndPoke\Component\Slumber\Annotation\IndexDefinition;
use PeekAndPoke\Component\Slumber\Annotation\PropertyStorageIndexMarker;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsObject;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\Store\AsDbReference;
use PeekAndPoke\Component\Slumber\Core\LookUp\PropertyMarkedForIndexing;
use PeekAndPoke\Component\Slumber\Core\LookUp\PropertyMarkedForSlumber;
use Psr\Log\LoggerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbIndexer
{
    /** @var MongoDbEntityConfigReader */
    private $lookUp;
    /** @var LoggerInterface */
    private $logger;

    /**
     * MongoDbIndexer constructor.
     *
     * @param MongoDbEntityConfigReader $lookUp
     * @param LoggerInterface           $logger
     */
    public function __construct(MongoDbEntityConfigReader $lookUp, LoggerInterface $logger)
    {
        $this->lookUp = $lookUp;
        $this->logger = $logger;
    }

    /**
     * @param Collection       $collection
     * @param \ReflectionClass $class
     *
     * @return array
     */
    public function ensureIndexes(Collection $collection, \ReflectionClass $class)
    {
        $createdIndexes = $this->ensureIndexesRecursive([], $class, $collection);

        // delete all indexes that are no longer needed
        $indexInfos     = $collection->listIndexes();
        $deletedIndexes = [];

        /** @var IndexInfo $indexInfo */
        foreach ($indexInfos as $indexInfo) {

            $indexInfoName = $indexInfo->getName();

            if ($indexInfoName !== '_id_' && ! in_array($indexInfoName, $createdIndexes, true)) {
                $collection->dropIndex($indexInfoName);
                $deletedIndexes[] = $indexInfoName;
            }
        }

        $this->logger->debug(
            'Creating indexes for repository ' . $collection->getCollectionName() . ' - ' .
            'Ensured indexes: ' . implode(', ', $createdIndexes) . ' - ' .
            'Deleted indexes: ' . implode(', ', $deletedIndexes)
        );

        return Psi::it($collection->listIndexes())->toArray();
    }

    /**
     * @param string[]         $prefixes
     * @param \ReflectionClass $entity
     * @param Collection       $collection
     *
     * @return array
     */
    private function ensureIndexesRecursive($prefixes, \ReflectionClass $entity, Collection $collection)
    {
        $entityConfig = $this->lookUp->getEntityConfig($entity);

        $createdIndexes = array_merge(
            $this->createCompoundIndexes($prefixes, $collection, $entityConfig->getCompoundIndexes()),
            $this->createPropertyIndexes($prefixes, $collection, $entityConfig->getIndexedProperties())
        );

        // also index child objects
        Psi::it($entityConfig->getMarkedProperties())
            ->filter(new IsInstanceOf(PropertyMarkedForSlumber::class))
            // we also look into child objects
            ->filter(function (PropertyMarkedForSlumber $p) {
                return $p->getFirstMarkerOf(AsObject::class) !== null;
            })
            // but NOT if they are db-references
            ->filter(function (PropertyMarkedForSlumber $p) {
                return $p->getFirstMarkerOf(AsDbReference::class) === null;
            })
            ->each(function (PropertyMarkedForSlumber $p) use ($prefixes, $collection, &$createdIndexes) {
                /** @var AsObject $asObject */
                $asObject = $p->getFirstMarkerOf(AsObject::class);

                $subCreated = $this->ensureIndexesRecursive(
                    array_merge($prefixes, [$p->alias]),
                    new \ReflectionClass($asObject->value),
                    $collection
                );

                $createdIndexes = array_merge($createdIndexes, $subCreated);
            })->toArray();

        return $createdIndexes;
    }

    /**
     * @param string[]                  $prefixes
     * @param Collection                $collection
     * @param CompoundIndexDefinition[] $compoundIndexes
     *
     * @return string[] The names of all indexes that where created
     */
    private function createCompoundIndexes($prefixes, Collection $collection, $compoundIndexes)
    {
        $createdIndexes = [];

        foreach ($compoundIndexes as $compoundIndex) {

            $definition = [];
            // append the prefixes to all fields
            foreach ($compoundIndex->getDefinition() as $k => $v) {
                $definition[$this->buildFieldName($prefixes, $k)] = $v;
            }

            $name    = $this->buildCompoundIndexName($prefixes, $definition);
            $options = $this->assembleOptions($name, $compoundIndex);

            $createdIndexes[] = $this->createIndex($collection, $definition, $options);
        }

        return $createdIndexes;
    }

    /**
     * @param string[]                    $prefixes
     * @param Collection                  $collection
     * @param PropertyMarkedForIndexing[] $indexedProperties
     *
     * @return string[] The names of all indexes that where created
     */
    private function createPropertyIndexes($prefixes, Collection $collection, $indexedProperties)
    {
        $createdIndexes = [];

        foreach ($indexedProperties as $indexedProperty) {

            foreach ($indexedProperty->markers as $marker) {

                $definition = [
                    // append the prefixes to the field
                    $this->buildFieldName($prefixes, $indexedProperty->propertyName) => $this->mapDirection($marker),
                ];
                $name       = $this->buildPropertyIndexName($prefixes, $indexedProperty->propertyName, $marker);
                $options    = $this->assembleOptions($name, $marker);

                $createdIndexes[] = $this->createIndex($collection, $definition, $options);
            }
        }

        return $createdIndexes;
    }

    /**
     * @param Collection $collection
     * @param array      $fields
     * @param array      $options
     *
     * @return string The resulting index names
     */
    private function createIndex(Collection $collection, array $fields, array $options)
    {
        $debugInfo = 'collection "' . $collection->getCollectionName() . '" for fields: ' . json_encode($fields) . ' - Options: ' . json_encode($options);

        try {
            $collection->createIndex($fields, $options);
        } catch (Exception\RuntimeException $e) {

            // Do we have a duplicate key problem?
            if ($e->getCode() === 11000 || strpos($e->getMessage(), 'E11000') !== false) {
                throw new \RuntimeException('Duplicate key problems on ' . $debugInfo, 0, $e);
            }

            // Have the options changes? So we try to drop the index and then create it again
            if ($e->getCode() === 85 || strpos($e->getMessage(), 'already exists with different options') === false) {
                // drop index by name
                $collection->dropIndex($options['name']);

                // and try the creation again
                try {
                    $collection->createIndex($fields, $options);
                } catch (Exception\RuntimeException $e) {
                    throw new \RuntimeException('Cannot create index - even after dropping it - on ' . $debugInfo, 0, $e);
                }
            }

            // something else has happened
            throw new \RuntimeException('Unknown problem on ' . $debugInfo, 0, $e);
        }

        return $options['name'];
    }

    /**
     * @param string          $indexName
     * @param IndexDefinition $marker
     *
     * @return array
     */
    private function assembleOptions($indexName, IndexDefinition $marker)
    {
        $options = [
            'name'       => (string) $indexName,
            'background' => (bool) $marker->isBackground(),
            'unique'     => (bool) $marker->isUnique(),
            'dropDups'   => (bool) $marker->isDropDups(),
            'sparse'     => (bool) $marker->isSparse(),
        ];

        if ($marker->getExpireAfterSeconds() >= 0) {
            $options['expireAfterSeconds'] = $marker->getExpireAfterSeconds();
        }

        return $options;
    }

    /**
     * @param string[] $prefixes
     * @param string   $fieldName
     *
     * @return string
     */
    private function buildFieldName($prefixes, $fieldName)
    {
        if (count($prefixes) === 0) {
            return $fieldName;
        }

        return implode('.', $prefixes) . '.' . $fieldName;
    }

    /**
     * @param string[]        $prefixes
     * @param string          $propertyName
     * @param IndexDefinition $marker
     *
     * @return string
     */
    private function buildPropertyIndexName($prefixes, $propertyName, IndexDefinition $marker)
    {
        // is the name overridden by the user ?
        if (! empty($marker->getName())) {
            $rest = $marker->getName();
        } else {
            $rest = $propertyName . '_' . $this->mapDirection($marker);
        }

        if (count($prefixes) > 0) {
            return implode('.', $prefixes) . '.' . $rest;
        }

        return $rest;
    }

    /**
     * @param string[] $prefixes
     * @param array    $definition
     *
     * @return string
     */
    private function buildCompoundIndexName($prefixes, $definition)
    {
        $parts = [];

        foreach ($definition as $k => $v) {
            $parts[] = ((string) $k) . '_' . ((string) $v);
        }

        $rest = implode('_', $parts);

        if (count($prefixes) > 0) {
            return implode('.', $prefixes) . '.' . $rest;
        }

        return $rest;
    }

    /**
     * @param IndexDefinition $marker
     *
     * @return int
     */
    private function mapDirection(IndexDefinition $marker)
    {
        return $marker->getDirection() === PropertyStorageIndexMarker::ASCENDING ? 1 : -1;
    }
}
