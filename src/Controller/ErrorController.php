<?php
declare(strict_types=1);

namespace MiniApi\Controller;

use MiniApi\Response\Factory\XmlResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ErrorController
{
    private XmlResponseFactoryInterface $responseFactory;

    /**
     * @param XmlResponseFactoryInterface $responseFactory
     */
    public function __construct(XmlResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function show(\Throwable $exception, LoggerInterface $logger): Response
    {
        return $this->responseFactory->createErrorResponse($exception->getMessage());
    }
}