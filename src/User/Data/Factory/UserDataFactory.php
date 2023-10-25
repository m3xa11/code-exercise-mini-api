<?php
declare(strict_types=1);

namespace MiniApi\User\Data\Factory;

use MiniApi\User\Data\UserData;
use MiniApi\User\Data\UserDataInterface;

class UserDataFactory implements UserDataFactoryInterface
{
    public function create(
        string $firstName,
        string $lastName,
        string $phone,
        string $email,
        string $country
    ): UserDataInterface {
        return new UserData($firstName, $lastName, $phone, $email, $country);
    }
}