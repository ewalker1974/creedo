<?php

namespace Creedo\App\Attribute;

use Attribute;
use RMValidator\Attributes\Base\BaseAttribute;
use RMValidator\Attributes\Base\IAttribute;
use RMValidator\Enums\SeverityEnum;
use RMValidator\Exceptions\BiggerException;
use RMValidator\Exceptions\NotANumberException;
use RMValidator\Exceptions\NotNullableException;
use Creedo\App\Enum\ValueType;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::TARGET_CLASS_CONSTANT | Attribute::TARGET_PARAMETER)]
class BiggerAttribute extends BaseAttribute implements IAttribute
{
    public function __construct(public float $biggerThan, protected ?string $errorMsg = null, protected ?string $customName = null, protected ?bool $nullable = false, protected ?string $name = null, protected ?int $severity = SeverityEnum::ERROR)
    {
        parent::__construct($errorMsg, $customName, $nullable, $name, $severity);
    }

    /**
     * @throws NotNullableException
     * @throws BiggerException
     * @throws NotANumberException
     */
    public function validate(mixed $value): void
    {
        if ($value === ValueType::UNDEFINED) {
            return;
        }

        if ($value === null) {
            if (!$this->checkNullable($value)) {
                throw new NotNullableException();
            }
            return;
        }
        if (!is_numeric($value)) {
            throw new NotANumberException();
        }

        if ($value <= $this->biggerThan) {
            throw new BiggerException($value, (int) $this->biggerThan);
        }
    }
}
