<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
        $shippingGateway = $this->shippingGatewayRepository->findOneByCode(OrganizationIdResolverInterface::INPOST_CODE);
        $config = $shippingGateway->getConfig();

        if (null === $config){
            throw new \Exception('Can not found config data');
        }

        return $config['is_quick_return'] ?? false;
    }
}
