<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Checker;

use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class ShippingMethodChecker implements ShippingMethodCheckerInterface
{
    public function isInPost(OrderInterface $order): bool
    {
        return $order->getShipments()->exists(function (int $index, ShipmentInterface $shipment): bool {
            if (null === $shipment->getMethod()) {
                return false;
            }

            $shipmentCode = $shipment->getMethod()->getCode();
            $isInPostPoint = ShippingExportEventListener::INPOST_POINT_SHIPPING_GATEWAY_CODE === $shipmentCode;
            $isInPost = ShippingExportEventListener::INPOST_SHIPPING_GATEWAY_CODE === $shipmentCode;

            return $isInPostPoint || $isInPost;
        });
    }
}
