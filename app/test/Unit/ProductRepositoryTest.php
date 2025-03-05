<?php

namespace Creedo\App\Test\Unit;

use Creedo\App\Db\MongoDBConnection;
use Creedo\App\Repository\MongoCrudProductRepository;
use DateTime;
use DateTimeInterface;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#
class ProductRepositoryTest extends TestCase
{
    private MongoCrudProductRepository $mongoCrudProductRepository;


    #[Test]
    public function findById_returnProduct(): void
    {
        $product = $this->mongoCrudProductRepository->findById('507f191e810c19729de860ea');

        $this->assertEquals('507f191e810c19729de860ea', $product->getId());
        $this->assertEquals('Test Product', $product->getName());
        $this->assertCount(2, $product->getAttributes());
        $this->assertEquals('2022-01-01T12:00:00+00:00', $product->getCreatedAt()->format(DateTimeInterface::RFC3339));
    }

    /**
     * @throws \Exception
     */
    #[Test]
    public function findAll_returnArrayOfProducts(): void
    {
        $products = $this->mongoCrudProductRepository->findAll();

        $this->assertCount(1, $products);
        $this->assertEquals('507f191e810c19729de860ea', $products[0]->getId());
        $this->assertEquals('Test Product', $products[0]->getName());
        $this->assertCount(2, $products[0]->getAttributes());
        $this->assertEquals('2022-01-01T12:00:00+00:00', $products[0]->getCreatedAt()->format(DateTimeInterface::RFC3339));
    }
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $document = new BSONDocument([
            '_id' => new ObjectId('507f191e810c19729de860ea'),
            'name' => 'Test Product',
            'price' => 19.99,
            'category' => 'Electronics',
            'attributes' => ['color' => 'Black', 'size' => 'Large'],
            'createdAt' => new UTCDateTime(new DateTime('2022-01-01T12:00:00')),
        ]);

        $collection = $this->createConfiguredStub(
            Collection::class,
            [
                'find' => [
                    $document,
                ],
                'findOne' => $document,


            ]
        );
        $connection = $this->createConfiguredStub(
            MongoDBConnection::class,
            [
                'getCollection' => $collection
            ]
        );

        $this->mongoCrudProductRepository = new MongoCrudProductRepository($connection);
    }
}
