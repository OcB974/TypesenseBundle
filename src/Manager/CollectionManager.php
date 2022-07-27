<?php

declare(strict_types=1);

namespace ACSEO\TypesenseBundle\Manager;

use ACSEO\TypesenseBundle\Client\CollectionClient;
use ACSEO\TypesenseBundle\Transformer\AbstractTransformer;

class CollectionManager
{
    private array $collectionDefinitions;
    private CollectionClient $collectionClient;
    private AbstractTransformer $transformer;

    public function __construct(CollectionClient $collectionClient, AbstractTransformer $transformer, array $collectionDefinitions)
    {
        $this->collectionDefinitions = $collectionDefinitions;
        $this->collectionClient      = $collectionClient;
        $this->transformer           = $transformer;
    }

    public function getCollectionDefinitions(): array
    {
        return $this->collectionDefinitions;
    }

    public function getManagedClassNames(): array
    {
        $managedClassNames = [];
        foreach ($this->collectionDefinitions as $name => $collectionDefinition) {
            $managedClassNames[$name] = $collectionDefinition['entity'];
        }

        return $managedClassNames;
    }

    public function getAllCollections()
    {
        return $this->collectionClient->list();
    }

    public function createAllCollections(): void
    {
        foreach ($this->collectionDefinitions as $name => $collectionDefinition) {
            $this->createCollection($name);
        }
    }

    public function deleteCollextion($collectionDefinitionName): void
    {
        $definition = $this->collectionDefinitions[$collectionDefinitionName];
        $this->collectionClient->delete($definition['typesense_name']);
    }

    public function createCollection($collectionDefinitionName): void
    {
        $definition       = $this->collectionDefinitions[$collectionDefinitionName];
        $fieldDefinitions = $definition['fields'];
        $fields           = [];
        foreach ($fieldDefinitions as $key => $fieldDefinition) {
            $fieldDefinition['type'] = $this->transformer->castType($fieldDefinition['type']);
            $fields[]                = $fieldDefinition;
        }

        $this->collectionClient->create(
            $definition['typesense_name'],
            $fields,
            $definition['default_sorting_field']
        );
    }
}
