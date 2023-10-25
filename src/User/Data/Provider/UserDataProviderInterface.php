<?php
declare(strict_types=1);

namespace MiniApi\User\Data\Provider;

use MiniApi\Exception\CustomException;
use MiniApi\User\Data\UserDataInterface;

interface UserDataProviderInterface
{
    /**
     * @param int $limit
     * @throws CustomException
     *
     * @return array
     */
    public function getUsersData(int $limit): array;
}