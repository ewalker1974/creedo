<?php

namespace Creedo\App\Controller;

use Creedo\App\Attribute\Route;
use Creedo\App\Dto\HttpResponse;
use Creedo\App\Dto\Product;
use Creedo\App\Dto\ProductInsertRequest;
use Creedo\App\Dto\ProductUpdateRequest;
use Creedo\App\Enum\HttpCode;
use Creedo\App\Enum\RequestMethod;
use Creedo\App\Exception\ProductNotFoundException;
use Creedo\App\Exception\ProductOperationException;
use Creedo\App\Service\ProductService;
use Psr\Log\LoggerInterface;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', description: 'Pure php implementation of REST API endpoints', title: 'Products CRUD endpoints')]
readonly class ProductController
{
    public function __construct(private ProductService $productService, private LoggerInterface $logger)
    {
    }

    #[OA\Get(path: '/products', summary: 'Get list of products')]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(type: 'array', items: new OA\Items(type: Product::class))
        )]
    )]

    #[OA\Response(
        response: 500,
        description: 'server error'
    )]
    #[Route(method: RequestMethod::GET, path: '/products')]
    public function getAllProducts(): HttpResponse
    {
        $this->logger->info('Get All products started');
        $products = $this->productService->listProducts();
        $this->logger->info('Get All products finished');
        return new HttpResponse(HttpCode::HTTP_OK, [], $products);
    }

    /**
     * @throws ProductNotFoundException
     */
    #[OA\Get(path: '/products/{id}', summary: 'Get particular product')]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(ref: Product::class, type: 'object')
        )]
    )]

    #[OA\Response(
        response: 404,
        description: 'product not found'
    )]

    #[OA\Response(
        response: 500,
        description: 'server error'
    )]
    #[Route(method: RequestMethod::GET, path: '/products/{id}')]
    public function getProductById(
        #[OA\PathParameter]
        string $id
    ): HttpResponse {
        $this->logger->info('Get product by id started', ['id' => $id]);
        $product = $this->productService->findProduct($id);
        $this->logger->info('Get product by id finished');

        return new HttpResponse(HttpCode::HTTP_OK, [], $product);
    }

    /**
     * @param array<string, mixed> $body
     * @throws ProductOperationException
     */
    #[OA\Post(path: '/products', summary: 'Create new product')]
    #[OA\RequestBody(
        description: 'New product',
        required: true,
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                ref: ProductInsertRequest::class,
                type: 'object'
            )
        )]
    )]
    #[OA\Response(
        response: 201,
        description: 'product created',
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(ref: Product::class, type: 'object')
        )]
    )]

    #[OA\Response(
        response: 400,
        description: 'bad request'
    )]

    #[OA\Response(
        response: 500,
        description: 'server error'
    )]
    #[Route(method: RequestMethod::POST, path: '/products')]
    public function insertProduct(array $body): HttpResponse
    {
        $this->logger->info('Get insert product started', ['body' => $body]);
        $productRequest = $this->arrayToInsertProduct($body);
        $product = $this->productService->insertProduct($productRequest);
        $this->logger->info('Get insert product finished');

        return new HttpResponse(HttpCode::HTTP_CREATED, [], $product);
    }

    /**
     * @param array<string, mixed> $body
     * @throws ProductNotFoundException
     */
    #[OA\Patch(path: '/products/{id}', summary: 'Edit existing product')]
    #[Route(method: RequestMethod::PATCH, path: '/products/{id}')]
    #[OA\RequestBody(
        description: 'Updated product',
        required: true,
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                ref: ProductUpdateRequest::class,
                type: 'object'
            )
        )]
    )]
    #[OA\Response(
        response: 200,
        description: 'product updated',
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(ref: Product::class, type: 'object')
        )]
    )]

    #[OA\Response(
        response: 404,
        description: 'product not found'
    )]

    #[OA\Response(
        response: 400,
        description: 'bad request'
    )]

    #[OA\Response(
        response: 500,
        description: 'server error'
    )]
    public function updateProduct(
        #[OA\PathParameter]
        string $id,
        array $body
    ): HttpResponse {
        $this->logger->info('Get update product started', ['id' => $id,  'body' => $body]);
        $productRequest = $this->arrayToUpdateProduct($body);
        $product = $this->productService->updateProduct($id, $productRequest);
        $this->logger->info('Get update product finished');

        return new HttpResponse(HttpCode::HTTP_OK, [], $product);
    }

    /**
     * @throws ProductNotFoundException
     */
    #[OA\Delete(path: '/products/{id}', summary: 'Delete existing product')]
    #[Route(method: RequestMethod::DELETE, path: '/products/{id}')]
    #[OA\Response(
        response: 204,
        description: 'product deleted'
    )]

    #[OA\Response(
        response: 404,
        description: 'product not found'
    )]

    #[OA\Response(
        response: 500,
        description:'server error'
    )]
    #[Route(method: RequestMethod::DELETE, path: '/products/{id}')]
    public function deleteProduct(
        #[OA\PathParameter]
        string $id
    ): HttpResponse {
        $this->logger->info('Get delete product started', ['id' => $id]);
        $this->productService->deleteProduct($id);
        $this->logger->info('Get delete product finished');

        return new HttpResponse(HttpCode::HTTP_NO_CONTENT);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function arrayToInsertProduct(array $data): ProductInsertRequest
    {
        return new ProductInsertRequest(
            name: $data['name'] ?? null,
            price: $data['price'] ?? null,
            category: $data['category'] ?? null,
            attributes: $data['attributes'] ?? null
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function arrayToUpdateProduct(array $data): ProductUpdateRequest
    {
        $product =  new ProductUpdateRequest();

        foreach ($data as $field => $value) {
            $setter = 'set' . ucfirst($field);

            if (method_exists($product, $setter)) {
                $product->{$setter}($value);
            }
        }

        return $product;
    }
}
