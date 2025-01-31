<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class GatewayGetPraticienByIdAction extends AbstractAction
{
    private ClientInterface $praticienClient;

    public function __construct(ClientInterface $client)
    {
        $this->praticienClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        if (empty($id)) {
            $response->getBody()->write(json_encode(['error' => "L'ID du praticien est requis."]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
        try {
            $queryParams = $request->getQueryParams();

            if (empty($queryParams)) {
                $response = $this->praticienClient->get("praticiens/$id");
            }
        } catch (ClientException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
        return $response;
    }
}
