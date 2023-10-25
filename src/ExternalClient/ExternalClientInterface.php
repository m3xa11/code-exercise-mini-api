<?php
declare(strict_types=1);

namespace MiniApi\ExternalClient;

interface ExternalClientInterface
{
    public function getData(): array;
}