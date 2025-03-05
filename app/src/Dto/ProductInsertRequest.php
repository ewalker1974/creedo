<?php

namespace Creedo\App\Dto;

use RMValidator\Attributes\PropertyAttributes\Global\RequiredAttribute;
use RMValidator\Attributes\PropertyAttributes\Numbers\BiggerAttribute;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'Product Insert', description: 'A Product.')]
readonly class ProductInsertRequest
{
    public function __construct(
        #[RequiredAttribute(errorMsg: 'Product name should be set')]
        private ?string $name = null,
        #[BiggerAttribute(biggerThan: 0, errorMsg: 'Product price should be set and be bigger than 0')]
        private ?float $price = null,
        #[RequiredAttribute(errorMsg: 'Product category should be set')]
        private ?string $category = null,
        /** @var array<string, string>|null */
        private ?array $attributes = null
    ) {
    }

    #[OA\Property(type: 'string', example: 'ajax')]
    public function getName(): ?string
    {
        return $this->name;
    }

    #[OA\Property(type: 'number', example: '123.22')]
    public function getPrice(): ?float
    {
        return $this->price;
    }

    #[OA\Property(type: 'string', example: 'software')]
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @return array<string, string>|null
     */
    #[OA\Property(type: 'object', additionalProperties: new OA\AdditionalProperties(type: 'string'))]
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }
}
