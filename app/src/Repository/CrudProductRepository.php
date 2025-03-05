<?php

namespace Creedo\App\Repository;

use Creedo\App\Dto\Product;

interface CrudProductRepository
{
    public function save(Product $product): Product;

    public function delete(Product $product): void;

    public function findById(string $id): ?Product;

    /**
     * @return Product[]
     */
    public function findAll(): array;
}
