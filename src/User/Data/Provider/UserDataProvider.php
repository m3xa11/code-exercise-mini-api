<?php
declare(strict_types=1);

namespace MiniApi\User\Data\Provider;

use MiniApi\ExternalClient\ExternalClientInterface;

class UserDataProvider implements UserDataProviderInterface
{
    private ExternalClientInterface $externalClient;

    public function __construct(ExternalClientInterface $externalClient)
    {
        $this->externalClient = $externalClient;
    }

    public function getUsersData(int $limit): array
    {
        $list = [];
        while (count($list) < $limit) {
            $list[] = $this->externalClient->getData();
        };

        usort($list, fn ($a, $b) => strcmp($b->getLastName(), $a->getLastName()));

        return $list;
    }
}