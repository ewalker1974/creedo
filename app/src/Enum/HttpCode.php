<?php

namespace Creedo\App\Enum;

enum HttpCode: int
{
    case HTTP_OK = 200;
    case HTTP_CREATED = 201;
    case HTTP_NO_CONTENT = 204;
    case HTTP_BAD_REQUEST = 400;
    case HTTP_NOT_FOUND = 404;
    case HTTP_INTERNAL_SERVER_ERROR = 500;
}
