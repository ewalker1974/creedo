<?php

namespace Creedo\App\Service;

use Creedo\App\Dto\Product;
use Creedo\App\Dto\ProductInsertRequest;
use Creedo\App\Dto\ProductUpdateRequest;
use Creedo\App\Exception\ProductNotFoundException;
use Creedo\App\Exception\ProductOperationException;
use Creedo\App\Repository\CrudProductRepository;
use Exception;
use RMValidator\Validators\MasterValidator;

readonly class ProductService
{
    public function __construct(private CrudProductRepository $productRepository)
    {
    }

    /**
     * @throws ProductNotFoundException
     */
    public function findProduct(string $id): Product
    {
        $product = $this->productRepository->findById($id);

        if (!$product instanceof Product) {
            throw new ProductNotFoundException($id);
        }

        return $product;
    }

    /**
     * @return Product[]
     *
     */
    public function listProducts(): array
    {
        return $this->productRepository->findAll();
    }

    /**
     * @throws ProductOperationException
     * @throws Exception
     */
    public function insertProduct(ProductInsertRequest $productInsert): Product
    {
        ValidationUtil::validate($productInsert);

        $product = ProductMap::fromInsertRequestToProduct($productInsert);

        return $this->productRepository->save($product);
    }

    /**
     * @throws ProductNotFoundException
     * @throws Exception
     */
    public function updateProduct(string $id, ProductUpdateRequest $productUpdate): Product
    {
        ValidationUtil::validate($productUpdate);

        $product = $this->findProduct($id);

        $product = ProductMap::fromUpdateRequestToProduct($product, $productUpdate);

        return $this->productRepository->save($product);
    }

    /**
     * @throws ProductNotFoundException
     */
    public function deleteProduct(string $id): void
    {
        $product = $this->findProduct($id);

        $this->productRepository->delete($product);
    }
}
