<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Resolver;

use BitBag\SyliusShippingExportPlugin\Repository\ShippingGatewayRepositoryInterface;

final class IsQuickReturnResolver implements IsQuickReturnResolverInterface
{
    private ShippingGatewayRepositoryInterface $shippingGatewayRepository;

    public function __construct(ShippingGatewayRepositoryInterface $shippingGatewayRepository)
    {
        $this->shippingGatewayRepository = $shippingGatewayRepository;
    }

    public function getIsQuickReturn(): bool
    {
        $shippingGateway = $this->shippingGatewayRepository
            ->findOneByCode(OrganizationIdResolverInterface::INPOST_CODE);

        $config = $shippingGateway->getConfig();

        if (null === $config) {
            throw new \Exception('Can not found config data');
        }

        return $config['is_quick_return'] ?? false;
    }
}
