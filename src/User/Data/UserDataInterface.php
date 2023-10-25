<?php
declare(strict_types=1);

namespace MiniApi\User\Data;

interface UserDataInterface
{
    public const FIELD_FULL_NAME = 'full_name';
    public const FIELD_PHONE = 'phone';
    public const FIELD_EMAIL = 'email';
    public const FIELD_COUNTRY = 'country';

    public function getFirstName(): string;
    public function getLastName(): string;
    public function getPhone(): string;
    public function getEmail(): string;
    public function getCountry(): string;
    public function toXmlData(): array;
}