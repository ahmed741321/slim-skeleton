<?php

namespace API\src\utilities\functions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

function result_message($result)
{
    return json_encode($result);
}

/**************************************************************************************************************/

function errorResponse(ResponseInterface $response, $message, $statusCode): ResponseInterface
{
    $result = [
        'Error' => $message,
        'Status' => false,
        'Code' => $statusCode
    ];
    $response->getBody()->write(result_message($result));
    $response = $response->withStatus($statusCode);
    $response = $response->withHeader('Content-Type', 'application/json');
    return $response;
}


/**************************************************************************************************************/

function successResponse(ResponseInterface $response, $data, $statusCode = 200): ResponseInterface
{
    $response->getBody()->write(json_encode($data));
    return $response->withStatus($statusCode)->withHeader('Content-Type', 'application/json');
}
