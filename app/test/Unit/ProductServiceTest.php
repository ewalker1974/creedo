<?php

namespace Creedo\App\Test\Unit;

use Creedo\App\Dto\Product;
use Creedo\App\Dto\ProductInsertRequest;
use Creedo\App\Dto\ProductUpdateRequest;
use Creedo\App\Enum\ValueType;
use Creedo\App\Exception\ProductNotFoundException;
use Creedo\App\Exception\ProductOperationException;
use Creedo\App\Repository\CrudProductRepository;
use Creedo\App\Service\ProductService;
use DateTime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RMValidator\Exceptions\Base\IValidationException;

class ProductServiceTest extends TestCase
{
    private ?ProductService $productService = null;


    /**
     * @throws ProductOperationException
     */
    #[Test]
    public function productInsert_noErrors(): void
    {
        $productRequest = new ProductInsertRequest(
            'Test Product',
            19.99,
            'Electronics',
            ['color' => 'Black', 'size' => 'Large']
        );

        $product = $this->productService->insertProduct($productRequest);

        $this->assertEquals('12345', $product->getId());
        $this->assertEquals('Test Product', $product->getName());
    }

    /**
     * @throws ProductOperationException
     */
    #[Test]
    public function productInsert_validationErrors(): void
    {
        $this->expectException(IValidationException::class);

        $productRequest = new ProductInsertRequest(
            null,
            19.99,
            'Electronics',
            ['color' => 'Black', 'size' => 'Large']
        );

        $this->productService->insertProduct($productRequest);
    }

    /**
     * @throws ProductNotFoundException
     */
    #[Test]
    public function productUpdate_noErrors(): void
    {
        $productRequest = (new ProductUpdateRequest())->setName('Test Product2');
        $product = $this->productService->updateProduct('12345', $productRequest);

        $this->assertEquals('12345', $product->getId());
        $this->assertEquals('Test Product2', $product->getName());
        $this->assertEquals('Electronics', $product->getCategory());
        $this->assertEquals(['color' => 'Black', 'size' => 'Large'], $product->getAttributes());
    }

    /**
     * @throws ProductNotFoundException
     */
    #[Test]
    public function testProductUpdate_validationErrors(): void
    {
        $this->expectException(IValidationException::class);

        $productRequest = (new ProductUpdateRequest())->setName(null);
        $this->productService->updateProduct('12345', $productRequest);
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        if ($this->productService === null) {
            $returnProduct = (new Product())
                ->setId('12345')
                ->setName('Test Product')
                ->setPrice(19.99)
                ->setCategory('Electronics')
                ->setAttributes(['color' => 'Black', 'size' => 'Large'])
                ->setCreatedAt(new DateTime());

            $productRepository = $this->createStub(CrudProductRepository::class);
            $productRepository
                ->method('save')
                ->willReturnCallback(
                    static fn (Product $product) => $product
                    ->setId($product->getId() === ValueType::UNDEFINED ? '12345' : $product->getId())
                );
            $productRepository
                ->method('findById')
                ->willReturn($returnProduct);

            $this->productService = new ProductService($productRepository);
        }
    }
}
