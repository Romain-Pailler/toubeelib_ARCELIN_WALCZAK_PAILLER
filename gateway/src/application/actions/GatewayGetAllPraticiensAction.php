<?php
namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class GatewayGetAllPraticiensAction extends AbstractAction
{
    private ClientInterface $praticienClient;

    public function __construct(ClientInterface $client) {
        $this->praticienClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            $queryParams = $request->getQueryParams();
        if (!empty($queryParams)) {
            $response = $this->praticienClient->get("praticiens", ['query' => $queryParams]);
        } else {
            $response = $this->praticienClient->get("praticiens");
        }
        } catch (ClientException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
        return $response;
    }
}
