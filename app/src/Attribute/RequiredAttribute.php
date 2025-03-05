<?php

namespace Creedo\App\Attribute;

use Attribute;
use RMValidator\Attributes\Base\BaseAttribute;
use RMValidator\Attributes\Base\IAttribute;
use RMValidator\Enums\SeverityEnum;
use RMValidator\Exceptions\NotNullableException;
use RMValidator\Exceptions\RequiredException;
use Creedo\App\Enum\ValueType;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::TARGET_CLASS_CONSTANT | Attribute::TARGET_PARAMETER)]
class RequiredAttribute extends BaseAttribute implements IAttribute
{
    public function __construct(protected ?string $errorMsg = null, protected ?string $customName = null, protected ?bool $nullable = false, protected ?string $name = null, protected ?int $severity = SeverityEnum::ERROR)
    {
        parent::__construct($errorMsg, $customName, $nullable, $name, $severity);
    }

    /**
     * @throws NotNullableException
     * @throws RequiredException
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
        if (empty($value)) {
            throw new RequiredException();
        }
    }
}
