<?php

namespace Creedo\App\Service;

use Creedo\App\Dto\Product;
use Creedo\App\Dto\ProductInsertRequest;
use Creedo\App\Dto\ProductUpdateRequest;
use Creedo\App\Enum\ValueType;
use DateTime;

final readonly class ProductMap
{
    private function __construct()
    {
    }

    public static function fromInsertRequestToProduct(ProductInsertRequest $request): Product
    {
        $product =  new Product();

        if (($name = $request->getName()) !== null) {
            $product->setName($name);
        }

        if (($price = $request->getPrice()) !== null) {
            $product->setPrice($price);
        }

        if (($category = $request->getCategory()) !== null) {
            $product->setCategory($category);
        }

        if (($attributes = $request->getAttributes()) !== null) {
            $product->setAttributes($attributes);
        }

        $product->setCreatedAt(new DateTime());

        return $product;
    }

    public static function fromUpdateRequestToProduct(Product $product, ProductUpdateRequest $request): Product
    {
        $name = $request->getName();

        if (is_string($name)) {
            $product->setName($name);
        }

        $price = $request->getPrice();

        if (is_float($price)) {
            $product->setPrice($price);
        }

        $category = $request->getCategory();

        if (is_string($category)) {
            $product->setCategory($category);
        }

        $attributes = $request->getAttributes();

        if (is_array($attributes)) {
            $product->setAttributes($attributes);
        }

        return $product;
    }

}
