<?php
namespace rdvs\infrastructure\api;

use GuzzleHttp\Client;
use rdvs\core\repositoryInterfaces\PraticienProviderInterface;

class PraticienApiAdapter implements PraticienProviderInterface {
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => 'http://api.gateway']);
    }

    public function getPraticienById(string $id): array
    {
        $response = $this->httpClient->get("/praticiens/{$id}");

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Praticien non trouvé");
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
