<?php

namespace Creedo\App\Test\Integration;

use Creedo\App\Bootstrap\AppBoot;
use Creedo\App\Controller\ProductController;
use Creedo\App\Db\MongoDBConnection;
use Creedo\App\DependencyInjection\Container;
use Creedo\App\Dto\ProductInsertRequest;
use Creedo\App\Enum\HttpCode;
use Creedo\App\Exception\ContainerException;
use Creedo\App\Exception\HttpException;
use Creedo\App\Exception\ProductNotFoundException;
use Creedo\App\Exception\ProductOperationException;
use Creedo\App\Service\ProductService;
use DateTime;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RMValidator\Exceptions\Base\IValidationException;

class ProductOperationsTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public static function setUpBeforeClass(): void
    {
        $boot = new AppBoot();
        $boot();
    }

    /**
     * @throws ContainerException
     * @throws Exception
     */
    #[Test]
    public function getAllProducts_returnList(): void
    {
        $container = Container::getInstance();
        /** @var MongoDBConnection $connection */
        $connection = $container->get(MongoDBConnection::class);
        $connection->getCollection('products')->insertMany([
            [
                'name' => 'Test Product 1',
                'description' => 'This is a test product.',
                'category' => 'Electronics',
                'price' => 9.99,
                'attributes' => ['test1' => 'test1', 'test2' => 'test2'],
                'createdAt' => new UTCDateTime(new DateTime())
            ],
            [
                'name' => 'Test Product 2',
                'description' => 'This is another test product.',
                'category' => 'Electronics',
                'price' => 19.99,
                'attributes' => ['test3' => 'test3', 'test4' => 'test4'],
                'createdAt' => new UTCDateTime(new DateTime())
            ]
        ]);

        $controller = new ProductController(
            $container->get(ProductService::class),
            $container->get(LoggerInterface::class)
        );

        $response = $controller->getAllProducts();

        $this->assertEquals(HttpCode::HTTP_OK, $response->statusCode);
        $this->assertIsArray($response->body);
        $this->assertCount(2, $response->body);

    }

    /**
     * @throws ContainerException
     * @throws Exception
     */
    #[Test]
    public function getProductById_returnProduct(): void
    {
        $container = Container::getInstance();
        /** @var MongoDBConnection $connection */
        $connection = $container->get(MongoDBConnection::class);
        $result = $connection
            ->getCollection('products')
            ->insertOne(
                [
                    'name' => 'Test Product 1',
                    'description' => 'This is a test product.',
                    'category' => 'Electronics',
                    'price' => 9.99,
                    'attributes' => ['test1' => 'test1', 'test2' => 'test2'],
                    'createdAt' => new UTCDateTime(new DateTime())
                ]
            );

        $controller = new ProductController(
            $container->get(ProductService::class),
            $container->get(LoggerInterface::class)
        );

        $response = $controller->getProductById($result->getInsertedId());

        $this->assertEquals(HttpCode::HTTP_OK, $response->statusCode);
        $this->assertEquals($result->getInsertedId(), $response->body->getId());
        $this->assertEquals('Test Product 1', $response->body->getName());
    }

    /**
     * @throws ProductNotFoundException
     * @throws ContainerException
     */
    #[Test]
    public function getProductById_returnNotFound(): void
    {
        $this->expectException(ProductNotFoundException::class);

        $container = Container::getInstance();

        $controller = new ProductController(
            $container->get(ProductService::class),
            $container->get(LoggerInterface::class)
        );

        $controller->getProductById('67c75c5a38dcdebb5c04dd82');
    }

    /**
     * @throws ProductOperationException
     * @throws ContainerException
     * @throws Exception
     */
    #[Test]
    public function createProduct_returnProduct(): void
    {
        $container = Container::getInstance();
        /** @var MongoDBConnection $connection */
        $connection = $container->get(MongoDBConnection::class);

        $productData = [
            'name' => 'Test Product 1',
            'description' => 'This is a test product.',
            'category' => 'Electronics',
            'price' => 9.99,
            'attributes' => ['test1' => 'test1', 'test2' => 'test2']
        ];

        $controller = new ProductController(
            $container->get(ProductService::class),
            $container->get(LoggerInterface::class)
        );

        $response = $controller->insertProduct($productData);

        $this->assertEquals(HttpCode::HTTP_CREATED, $response->statusCode);
        $this->assertEquals($productData['name'], $response->body->getName());

        $result = $connection->getCollection('products')->findOne(['_id' => new ObjectId($response->body->getId())]);
        $this->assertNotNull($result);
    }

    /**
     * @throws ProductOperationException
     * @throws ContainerException
     */
    #[Test]
    public function createProduct_returnValidationError(): void
    {
        $this->expectException(IValidationException::class);

        $container = Container::getInstance();

        $productData = [
            'description' => 'This is a test product.',
            'category' => 'Electronics',
            'price' => 9.99,
            'attributes' => ['test1' => 'test1', 'test2' => 'test2']
        ];

        $controller = new ProductController(
            $container->get(ProductService::class),
            $container->get(LoggerInterface::class)
        );

        $controller->insertProduct($productData);
    }

    /**
     * @throws ProductOperationException
     * @throws ContainerException
     * @throws ProductNotFoundException
     * @throws Exception
     */
    #[Test]
    public function updateProduct_returnProduct(): void
    {
        $container = Container::getInstance();
        /** @var MongoDBConnection $connection */
        $connection = $container->get(MongoDBConnection::class);
        $result = $connection
            ->getCollection('products')
            ->insertOne(
                [
                    'name' => 'Test Product 1',
                    'description' => 'This is a test product.',
                    'category' => 'Electronics',
                    'price' => 9.99,
                    'attributes' => ['test1' => 'test1', 'test2' => 'test2'],
                    'createdAt' => new UTCDateTime(new DateTime())
                ]
            );

        $productData = [
            'name' => 'Test Product 2',
        ];

        $controller = new ProductController(
            $container->get(ProductService::class),
            $container->get(LoggerInterface::class)
        );

        $response = $controller->updateProduct($result->getInsertedId(), $productData);

        $this->assertEquals(HttpCode::HTTP_OK, $response->statusCode);
        $this->assertEquals($productData['name'], $response->body->getName());

        /** @var BSONDocument $result */
        $result = $connection
            ->getCollection('products')
            ->findOne(['_id' => new ObjectId($response->body->getId())]);

        $this->assertNotNull($productData['name'], $result->offsetGet('name'));
    }

    /**
     * @throws ProductNotFoundException
     * @throws ContainerException
     * @throws Exception
     */
    #[Test]
    public function deleteProduct_returnNoContent(): void
    {
        $container = Container::getInstance();
        /** @var MongoDBConnection $connection */
        $connection = $container->get(MongoDBConnection::class);
        $result = $connection
            ->getCollection('products')
            ->insertOne(
                [
                    'name' => 'Test Product 1',
                    'description' => 'This is a test product.',
                    'category' => 'Electronics',
                    'price' => 9.99,
                    'attributes' => ['test1' => 'test1', 'test2' => 'test2'],
                    'createdAt' => new UTCDateTime(new DateTime())
                ]
            );

        $controller = new ProductController(
            $container->get(ProductService::class),
            $container->get(LoggerInterface::class)
        );

        $response = $controller->deleteProduct($result->getInsertedId());

        $this->assertEquals(HttpCode::HTTP_NO_CONTENT, $response->statusCode);

        $result = $connection
            ->getCollection('products')
            ->findOne(['_id' => new ObjectId($result->getInsertedId())]);

        $this->assertNull($result);
    }

    /**
     * @throws ProductNotFoundException
     * @throws ContainerException
     */
    #[Test]
    public function deleteProduct_returnNotFound(): void
    {
        $this->expectException(ProductNotFoundException::class);

        $container = Container::getInstance();
        /** @var MongoDBConnection $connection */
        $connection = $container->get(MongoDBConnection::class);

        $controller = new ProductController(
            $container->get(ProductService::class),
            $container->get(LoggerInterface::class)
        );

        $controller->deleteProduct('67c75c5a38dcdebb5c04dd82');
    }

    /**
     * @throws ContainerException
     * @throws Exception
     */
    protected function tearDown(): void
    {
        /** @var MongoDBConnection $connection */
        $connection = Container::getInstance()->get(MongoDBConnection::class);
        $connection->getCollection('products')->drop();
    }
}
