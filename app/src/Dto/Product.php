<?php

namespace Creedo\App\Dto;

use Creedo\App\Enum\ValueType;
use DateTimeInterface;
use JsonSerializable;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'Product Result', description: 'A Product')]
class Product implements JsonSerializable
{
    #[OA\Property(type: 'string', example: '1234567890')]
    private string|ValueType $id = ValueType::UNDEFINED;
    #[OA\Property(type: 'string', example: 'ajax')]
    private string $name;
    #[OA\Property(type: 'number', example: '123.22')]
    private float $price;

    #[OA\Property(type: 'string', example: 'software')]
    private string $category;

    /** @var array<string, string>|null */
    #[OA\Property(type: 'object', additionalProperties: new OA\AdditionalProperties(type: 'string'))]
    private ?array $attributes;

    #[OA\Property]
    private DateTimeInterface $createdAt;

    public function getId(): string|ValueType
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return array<string, string>|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, string>|null $attributes
     * @return $this
     */
    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id instanceof ValueType ? null : $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'category' => $this->category,
            'attributes' => $this->attributes,
            'createdAt' => $this->createdAt->format(DateTimeInterface::RFC3339),
        ];
    }
}
