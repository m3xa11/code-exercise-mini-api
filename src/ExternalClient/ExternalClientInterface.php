<?php
declare(strict_types=1);

namespace MiniApi\ExternalClient;

use MiniApi\Exception\CustomException;
use MiniApi\User\Data\UserDataInterface;

interface ExternalClientInterface
{
    /**
     * @throws CustomException
     *
     * @return UserDataInterface
     */
    public function getData(): UserDataInterface;
}