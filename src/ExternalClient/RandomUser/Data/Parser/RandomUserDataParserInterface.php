<?php
declare(strict_types=1);

namespace MiniApi\ExternalClient\RandomUser\Data\Parser;

use MiniApi\Exception\CustomException;
use MiniApi\User\Data\UserDataInterface;

interface RandomUserDataParserInterface
{
    public const RAW_DATA_FIELD_RESULTS = 'results';
    public const RAW_DATA_FIELD_NAME = 'name';
    public const RAW_DATA_FIELD_FIRST = 'first';
    public const RAW_DATA_FIELD_LAST = 'last';
    public const RAW_DATA_FIELD_LOCATION = 'location';
    public const RAW_DATA_FIELD_COUNTRY = 'country';
    public const RAW_DATA_FIELD_EMAIL = 'email';
    public const RAW_DATA_FIELD_PHONE = 'phone';

    /**
     * @param array $data
     * @throws CustomException
     * @return UserDataInterface
     */
    public function parse(array $data): UserDataInterface;
}