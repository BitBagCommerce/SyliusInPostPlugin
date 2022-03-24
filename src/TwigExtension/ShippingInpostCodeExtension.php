<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\TwigExtension;

use BitBag\SyliusInPostPlugin\Resolver\IsQuickReturnResolverInterface;
use BitBag\SyliusInPostPlugin\Resolver\OrganizationIdResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ShippingInpostCodeExtension extends AbstractExtension
{
    public IsQuickReturnResolverInterface $isQuickReturnResolver;

    public function __construct(IsQuickReturnResolverInterface $isQuickReturnResolver)
    {
        $this->isQuickReturnResolver = $isQuickReturnResolver;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('quick_return', [$this, 'isInpostShippingCode']),
        ];
    }

    public function isInpostShippingCode(OrderInterface $order)
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $order->getShipments()->first();
        if ($shipment) {
            /** @var ShippingMethodInterface $shippingMethod */
            $shippingMethod = $shipment->getMethod();
            $shipmentCode = $shippingMethod->getCode();
            $isQuickReturn = $this->isQuickReturnResolver->getIsQuickReturn();
            if ($isQuickReturn && (OrganizationIdResolverInterface::INPOST_CODE === $shipmentCode)) {

                return true;
            }
        }

        return false;
    }
}

