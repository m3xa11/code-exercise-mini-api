<?php
declare(strict_types=1);

namespace MiniApi\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController
{
    public function index(): JsonResponse
    {
        return new JsonResponse(["here"]);
    }
}