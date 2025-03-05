<?php

namespace Creedo\App\Db;

use Exception;
use MongoDB\Client;
use MongoDB\Collection;

class MongoDBConnection
{
    private readonly Client $client;

    public function __construct()
    {
        $this->client = new Client($_ENV['DB_URL'], [
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
        ]);
    }

    /**
     * @throws Exception
     */
    public function getCollection(string $collection): Collection
    {
        return $this->client->selectCollection($_ENV['DB_NAME'], $collection);
    }
}
