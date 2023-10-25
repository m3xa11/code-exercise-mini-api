<?php
declare(strict_types=1);

namespace MiniApi\Response\Factory;

use MiniApi\Exception\CustomException;
use MiniApi\User\Data\UserDataInterface;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class XmlResponseFactory implements XmlResponseFactoryInterface
{
    private SerializerInterface $serializer;
    private array $xmlHeaders = ['Content-Type' => 'application/xml;charset=UTF-8'];

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createSuccessResponse(UserDataInterface ...$list): Response
    {
        return new Response(
            $this->serializer->serialize(
                array_map(fn(UserDataInterface $userData) => $userData->toXmlData(), $list),
                XmlEncoder::FORMAT,
                [
                    XmlEncoder::ROOT_NODE_NAME => 'users',
                    XmlEncoder::ENCODING => 'UTF-8'
                ]
            ),
            Response::HTTP_OK,
            $this->xmlHeaders
        );
    }

    public function createFailedResponse(CustomException $exception): Response
    {
        return new Response(
            $this->serializer->serialize(
                [
                    'message' => $exception->getMessage(),
                    'previous' => json_encode($exception->getPrevious()),
                ],
                XmlEncoder::FORMAT,
                [
                    XmlEncoder::ROOT_NODE_NAME => 'error',
                    XmlEncoder::ENCODING => 'UTF-8'
                ]
            ),
            Response::HTTP_OK,
            $this->xmlHeaders
        );
    }

    public function createLimitParamInvalidResponse(string $limit): Response
    {
        return $this->createErrorResponse(
            sprintf('Param limit must be positive integer, provided value: %s ', $limit)
        );
    }

    public function createErrorResponse(string $message): Response
    {
        return new Response(
            $this->serializer->serialize(
                [
                    'message' => $message,
                ],
                XmlEncoder::FORMAT,
                [
                    XmlEncoder::ROOT_NODE_NAME => 'error',
                    XmlEncoder::ENCODING => 'UTF-8'
                ]
            ),
            Response::HTTP_OK,
            $this->xmlHeaders
        );
    }
}