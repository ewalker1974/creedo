<?php

namespace Creedo\App\Repository;

use Creedo\App\Db\MongoDBConnection;
use Creedo\App\Dto\Product;
use Creedo\App\Enum\ValueType;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

class MongoCrudProductRepository implements CrudProductRepository
{
    private const PRODUCT_COLLECTION = 'products';

    public function __construct(private readonly MongoDBConnection $connection)
    {
    }

    /**
     * @throws Exception
     */
    public function save(Product $product): Product
    {
        $data = $this->productToArray($product);
        if (($id = $product->getId()) === ValueType::UNDEFINED) {
            $result = $this->connection->getCollection(self::PRODUCT_COLLECTION)->insertOne($data);
            $product->setId($result->getInsertedId());
        } else {
            $this->connection
                ->getCollection(self::PRODUCT_COLLECTION)
                ->updateOne(['_id' => new ObjectId($id)], ['$set' => $data]);
        }

        return $product;
    }

    /**
     * @throws Exception
     */
    public function delete(Product $product): void
    {
        if (is_string($id = $product->getId())) {
            $this->connection->getCollection(self::PRODUCT_COLLECTION)->deleteOne(['_id' => new ObjectId($product->getId())]);
        }
    }

    /**
     * @throws Exception
     */
    public function findById(string $id): ?Product
    {
        $data = $this->connection->getCollection(self::PRODUCT_COLLECTION)->findOne(['_id' => new ObjectId($id)]);
        if (!$data  instanceof BSONDocument) {
            return null;
        }

        return $this->documentToProduct($data);
    }

    /**
     * @return Product[]
     * @throws Exception
     */
    public function findAll(): array
    {
        $items = $this->connection->getCollection(self::PRODUCT_COLLECTION)->find();

        return array_map(
            fn (BSONDocument $data): Product => $this->documentToProduct($data),
            iterator_to_array($items)
        );
    }

    /**
     * @return mixed[]
     */
    private function bsonDocumentToArray(BSONDocument $document): array
    {
        $data = $document->getArrayCopy();

        return array_map(
            fn (mixed $value) => $value instanceof BSONDocument ? $this->bsonDocumentToArray($value) : $value,
            $data
        );
    }

    private function documentToProduct(BSONDocument $document): Product
    {
        $data = $this->bsonDocumentToArray($document);

        $product = new Product();

        foreach ($data as $field => $value) {
            $setter = 'set'. ucfirst($field);
            if (method_exists($product, $setter)) {
                if ($value instanceof UTCDateTime) {
                    $product->{$setter}($value->toDateTime());
                } elseif ($value instanceof ObjectId) {
                    $product->{$setter}((string) $value);
                } else {
                    $product->{$setter}($value);
                }
            }
        }
        $product->setId($data['_id']);

        return $product;
    }

    /**
     * @return mixed[]
     */
    private function productToArray(Product $product): array
    {
        return [
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'category' => $product->getCategory(),
            'attributes' => $product->getAttributes(),
            'createdAt' => new UTCDateTime($product->getCreatedAt()),
        ];
    }
}
