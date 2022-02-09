<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\ObjectMother;

use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethod;

class ShipmentObjectMother
{
    public const FOO = 'foo';

    public const INPOST = 'inpost';

    public static function createWithShippingMethodWithFooCode(): ShipmentInterface
    {
        $shippingMethod = new ShippingMethod();
        $shippingMethod->setCode(self::FOO);

        $shipment = new Shipment();
        $shipment->setMethod($shippingMethod);

        return $shipment;
    }

    public static function createWithShippingMethodWithInPostCode(): ShipmentInterface
    {
        $shippingMethod = new ShippingMethod();
        $shippingMethod->setCode(self::INPOST);

        $shipment = new Shipment();
        $shipment->setMethod($shippingMethod);

        return $shipment;
    }
}
