<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\Checker;

use BitBag\SyliusInPostPlugin\Checker\ShippingMethodChecker;
use BitBag\SyliusInPostPlugin\Checker\ShippingMethodCheckerInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;
use PhpSpec\ObjectBehavior;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\OrderBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShipmentBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShippingMethodBuilder;

class ShippingMethodCheckerSpec extends ObjectBehavior
{
    public const NOT_INPOST = 'not_inpost';

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingMethodChecker::class);
        $this->shouldBeAnInstanceOf(ShippingMethodCheckerInterface::class);
    }

    function it_should_return_true_if_shipping_method_is_inpost(): void
    {
        $shippingMethod = ShippingMethodBuilder::create()
            ->withCode(ShippingExportEventListener::INPOST_SHIPPING_GATEWAY_CODE)
            ->build();
        $shipment = ShipmentBuilder::create()->withShippingMethod($shippingMethod)->build();
        $order = OrderBuilder::create()->withShipment($shipment)->build();

        $this->isInPost($order)->shouldReturn(true);
    }

    function it_should_return_true_if_shipping_method_is_inpost_point(): void
    {
        $shippingMethod = ShippingMethodBuilder::create()
            ->withCode(ShippingExportEventListener::INPOST_POINT_SHIPPING_GATEWAY_CODE)
            ->build();
        $shipment = ShipmentBuilder::create()->withShippingMethod($shippingMethod)->build();
        $order = OrderBuilder::create()->withShipment($shipment)->build();

        $this->isInPost($order)->shouldReturn(true);
    }

    function it_should_return_false_if_shipping_method_is_not_inpost(): void
    {
        $shippingMethod = ShippingMethodBuilder::create()
            ->withCode(self::NOT_INPOST)
            ->build();
        $shipment = ShipmentBuilder::create()->withShippingMethod($shippingMethod)->build();
        $order = OrderBuilder::create()->withShipment($shipment)->build();

        $this->isInPost($order)->shouldReturn(false);
    }
}
