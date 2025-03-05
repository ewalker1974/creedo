<?php

namespace Creedo\App\Http;

use Creedo\App\Dto\HttpResponse;

class JsonSender
{
    public function send(HttpResponse $response): void
    {
        http_response_code($response->statusCode->value);
        foreach ($response->headers as $header => $value) {
            header($header.': '.$value);
        }
        header('Content-Type: application/json');

        if ($response->body !== null) {
            echo json_encode($response->body);
        }
    }
}
