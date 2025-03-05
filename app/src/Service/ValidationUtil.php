<?php

namespace Creedo\App\Service;

use Exception;
use RMValidator\Enums\ValidationOrderEnum;
use RMValidator\Options\OptionsModel;
use RMValidator\Validators\MasterValidator;

final readonly class ValidationUtil
{
    private function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public static function validate(object $object): void
    {
        $options = new OptionsModel([ValidationOrderEnum::PROPERTIES]);
        MasterValidator::validate($object, $options);
    }

}
