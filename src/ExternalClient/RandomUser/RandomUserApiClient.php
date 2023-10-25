<?php
declare(strict_types=1);

namespace MiniApi\ExternalClient\RandomUser;

use GuzzleHttp\Client;
use MiniApi\Exception\CustomException;
use MiniApi\ExternalClient\ExternalClientInterface;
use MiniApi\ExternalClient\RandomUser\Data\Parser\RandomUserDataParserInterface;
use MiniApi\User\Data\UserDataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RandomUserApiClient implements ExternalClientInterface
{
    private const ENDPOINT = '/api';
    private Client $client;
    private RandomUserDataParserInterface $parser;

    public function __construct(Client $client, RandomUserDataParserInterface $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    public function getData(): UserDataInterface
    {
        try {
            $response = $this->client->request(Request::METHOD_GET, self::ENDPOINT);
        } catch (\Throwable $exception) {
            throw new CustomException('Request to external API failed', 0, $exception);
        }

        if (Response::HTTP_OK !== ($responseCode = $response->getStatusCode()) ) {
            throw new CustomException(sprintf('External Api responds with non OK code : %d', $responseCode));
        }

        return $this->parser->parse(json_decode($response->getBody()->getContents(), true));
    }
}