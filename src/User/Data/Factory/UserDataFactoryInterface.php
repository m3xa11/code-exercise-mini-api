<?php
declare(strict_types=1);

namespace MiniApi\User\Data\Factory;

use MiniApi\User\Data\UserDataInterface;

interface UserDataFactoryInterface
{
    public function create(
        string $firstName,
        string $lastName,
        string $phone,
        string $email,
        string $country
    ): UserDataInterface;
}