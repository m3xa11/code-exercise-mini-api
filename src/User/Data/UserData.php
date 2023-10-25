<?php
declare(strict_types=1);

namespace MiniApi\User\Data;

class UserData implements UserDataInterface
{
    private string $firstName;
    private string $lastName;
    private string $phone;
    private string $email;
    private string $country;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $phone
     * @param string $email
     * @param string $country
     */
    public function __construct(string $firstName, string $lastName, string $phone, string $email, string $country)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->email = $email;
        $this->country = $country;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function toXmlData(): array
    {
        return [
            self::FIELD_FULL_NAME => sprintf('%s %s', $this->firstName, $this->lastName),
            self::FIELD_PHONE => $this->phone,
            self::FIELD_EMAIL => $this->email,
            self::FIELD_COUNTRY => $this->country,
        ];
    }
}