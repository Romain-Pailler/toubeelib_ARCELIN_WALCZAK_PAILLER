<?php

namespace toubeelib\application\middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

class CorsMiddleware
{
    public function  __invoke(Request $request, RequestHandler $handler): Response{

        if (! $request->hasHeader('Origin'))
            New HttpUnauthorizedException ($request, "missing Origin Header (cors)");

        $origin = $request->getHeaderLine('Origin');
        $methode = $request->getMethod();
        $header = $request->getHeaders();
        $headerList = implode(', ', $header);


        $response = $handler->handle($request);
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', $methode )
            ->withHeader('Access-Control-Allow-Headers',$headerList )
            ->withHeader('Access-Control-Max-Age', 3600)
            ->withHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}