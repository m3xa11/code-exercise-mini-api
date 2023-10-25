<?php
declare(strict_types=1);

namespace MiniApi\Controller;

use MiniApi\Exception\CustomException;
use MiniApi\Response\Factory\XmlResponseFactoryInterface;
use MiniApi\User\Data\Provider\UserDataProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    private UserDataProviderInterface $provider;
    private XmlResponseFactoryInterface $responseFactory;

    public function __construct(UserDataProviderInterface $provider, XmlResponseFactoryInterface $responseFactory)
    {
        $this->provider = $provider;
        $this->responseFactory = $responseFactory;
    }

    public function users(Request $request): Response
    {
        $limit = $request->get('limit', 10);
        if (false === filter_var($limit, FILTER_VALIDATE_INT) || 1 > (int) $limit ) {
            return $this->responseFactory->createLimitParamInvalidResponse($limit);
        }

        try {
            $list = $this->provider->getUsersData((int) $limit);
        } catch (CustomException $exception) {
            return $this->responseFactory->createFailedResponse($exception);
        }

        return $this->responseFactory->createSuccessResponse(...$list);
    }
}