<?php
namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class GatewayGetPraticienByIdAction extends AbstractAction
{
    private ClientInterface $toubeelibClient;

    public function __construct(ClientInterface $client) {
        $this->toubeelibClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id = $args['id'];
        try {
            $queryParams = $request->getQueryParams();

            // Si des query params existent, les inclure dans la requête
            if (!empty($queryParams)) {
                $response = $this->toubeelibClient->get("praticiens/$id/disponibilites", ['query' => $queryParams]);
            } else {
                // Si aucun query param, envoyer une requête sans paramètres
                $response = $this->toubeelibClient->get("praticiens/$id");
            }

        } catch (ClientException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
        return $response;
    }
}
