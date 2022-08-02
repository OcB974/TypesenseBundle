<?php

declare(strict_types=1);

namespace ACSEO\TypesenseBundle\Manager;

use ACSEO\TypesenseBundle\Client\TypesenseClient;
use Exception;

class DocumentManager
{
    private TypesenseClient $client;

    public function __construct(TypesenseClient $client)
    {
        $this->client = $client;
    }

    public function retrieveDocument(string $collection, $id)
    {
        try {
            return $this->client->collections[$collection]->documents[$id]->retrieve();
        } catch( Exception $e ) {
        }
        return null ;
    }

    public function delete($collection, $id)
    {
        if (!$this->client->isOperationnal()) {
            return null;
        }

        return $this->client->collections[$collection]->documents[$id]->delete();
    }

    public function index($collection, $data)
    {
        if (!$this->client->isOperationnal()) {
            return null;
        }
        $retrieveDocument = $this->retrieveDocument($collection, $data['id']);
        if(empty($retrieveDocument)): // Not found document, create
            return $this->client->collections[$collection]->documents->create($data);
        endif;
    }

    public function import(string $collection, array $data, string $action = 'create'): array
    {
        if (!$this->client->isOperationnal() || empty($data)) {
            return [];
        }

        return $this->client->collections[$collection]->documents->import($data, ['action' => $action]);
    }
}
