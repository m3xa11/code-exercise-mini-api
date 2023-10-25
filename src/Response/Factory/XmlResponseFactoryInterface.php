<?php
declare(strict_types=1);

namespace MiniApi\Response\Factory;

use MiniApi\Exception\CustomException;
use MiniApi\User\Data\UserDataInterface;
use Symfony\Component\HttpFoundation\Response;

interface XmlResponseFactoryInterface
{
    public function createSuccessResponse(UserDataInterface...$list): Response;

    public function createFailedResponse(CustomException $exception): Response;

    public function createLimitParamInvalidResponse(string $limit) : Response;

    public function createErrorResponse(string $message): Response;
}