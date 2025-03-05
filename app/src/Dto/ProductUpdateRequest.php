<?php

namespace Creedo\App\Dto;

use Creedo\App\Attribute\BiggerAttribute;
use Creedo\App\Attribute\RequiredAttribute;
use Creedo\App\Enum\ValueType;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'Product Update', description: 'A Product.')]
class ProductUpdateRequest
{
    #[RequiredAttribute(errorMsg: 'Product name should be set')]
    private string|ValueType|null $name = ValueType::UNDEFINED;
    #[BiggerAttribute(biggerThan: 0, errorMsg: 'Product price should be set and be bigger than 0')]
    private float|ValueType|null $price = ValueType::UNDEFINED;
    #[RequiredAttribute(errorMsg: 'Product category should be set')]
    private string|ValueType|null $category = ValueType::UNDEFINED;
    /** @var ValueType|array<string, string>|null */
    private array|ValueType|null $attributes = ValueType::UNDEFINED;

    #[OA\Property(type: 'string', example: 'ajax')]

    public function getName(): ValueType|string|null
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    #[OA\Property(type: 'number', example: '123.22')]
    public function getPrice(): float|ValueType|null
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;
        return $this;
    }

    #[OA\Property(type: 'string', example: 'software')]
    public function getCategory(): string|ValueType|null
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return ValueType|array <string, string>|null
     */
    #[OA\Property(type: 'object', additionalProperties: new OA\AdditionalProperties(type: 'string'))]
    public function getAttributes(): ValueType|array|null
    {
        return $this->attributes;
    }

    /**
     * @param array <string, string>|null $attributes
     * @return $this
     */
    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }
}
