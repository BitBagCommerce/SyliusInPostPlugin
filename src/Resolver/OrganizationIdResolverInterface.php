<?php

namespace BitBag\SyliusInPostPlugin\Resolver;

interface OrganizationIdResolverInterface
{
    public const INPOST_CODE = 'inpost';

    public function getOrganizationId(int $orderId): string;
}
