<?php
declare(strict_types=1);

namespace MiniApi\ExternalClient\RandomUser\Data\Parser;

use MiniApi\Exception\CustomException;
use MiniApi\User\Data\Factory\UserDataFactoryInterface;
use MiniApi\User\Data\UserDataInterface;

class RandomUserDataParser implements RandomUserDataParserInterface
{
    private array $rootKeys = [
        self::RAW_DATA_FIELD_NAME,
        self::RAW_DATA_FIELD_LOCATION,
        self::RAW_DATA_FIELD_EMAIL,
        self::RAW_DATA_FIELD_PHONE
    ];

    private array $nameKeys = [self::RAW_DATA_FIELD_FIRST, self::RAW_DATA_FIELD_LAST];

    private UserDataFactoryInterface $factory;

    /**
     * @param UserDataFactoryInterface $factory
     */
    public function __construct(UserDataFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function parse(array $data): UserDataInterface
    {
        if (false === array_key_exists(self::RAW_DATA_FIELD_RESULTS, $data)) {
            $this->throwKeyAbsentException(self::RAW_DATA_FIELD_RESULTS, $data);
        }

        $results = $data[self::RAW_DATA_FIELD_RESULTS];
        if (false === is_array($results) || 1 !== count($results)) {
            $this->throwKeyUnexpectedValueException(self::RAW_DATA_FIELD_RESULTS, $data);
        }

        $rawUserData = $results[0];
        foreach ($this->rootKeys as $rootKey) {
            if (false === array_key_exists($rootKey, $rawUserData)) {
                $this->throwKeyAbsentException($rootKey, $data);
            }
        }

        $rawNameData = $rawUserData[self::RAW_DATA_FIELD_NAME];
        if (false === is_array($rawNameData)) {
            $this->throwKeyUnexpectedValueException(self::RAW_DATA_FIELD_NAME, $data);
        }

        foreach ($this->nameKeys as $nameKey) {
            if (false === array_key_exists($nameKey, $rawNameData)) {
                $this->throwKeyAbsentException($nameKey, $data);
            }

            if (false === is_string($rawNameData[$nameKey])) {
                $this->throwKeyUnexpectedValueException($nameKey, $data);
            }
        }

        $rawLocationData = $rawUserData[self::RAW_DATA_FIELD_LOCATION];
        if (false === is_array($rawLocationData)) {
            $this->throwKeyUnexpectedValueException(self::RAW_DATA_FIELD_LOCATION, $data);
        }

        if (false === array_key_exists(self::RAW_DATA_FIELD_COUNTRY, $rawLocationData)) {
            $this->throwKeyAbsentException(self::RAW_DATA_FIELD_COUNTRY, $data);
        }

        if (false === is_string($rawLocationData[self::RAW_DATA_FIELD_COUNTRY])) {
            $this->throwKeyUnexpectedValueException(self::RAW_DATA_FIELD_COUNTRY, $data);
        }

        return $this->factory->create(
            $rawNameData[self::RAW_DATA_FIELD_FIRST],
            $rawNameData[self::RAW_DATA_FIELD_LAST],
            $rawUserData[self::RAW_DATA_FIELD_PHONE],
            $rawUserData[self::RAW_DATA_FIELD_EMAIL],
            $rawLocationData[self::RAW_DATA_FIELD_COUNTRY]
        );
    }

    /**
     * @param string $key
     * @param array $data
     * @throws CustomException
     */
    private function throwKeyAbsentException(string $key, array $data) {
        throw new CustomException(
            sprintf('Key %s is absent in api user data %s', $key, json_encode($data))
        );
    }


    /**
     * @param string $key
     * @param array $data
     * @throws CustomException
     */
    private function throwKeyUnexpectedValueException(string $key, array $data) {
        throw new CustomException(
            sprintf('Key %s contains unexpected value in api user data %s', $key, json_encode($data))
        );
    }
}