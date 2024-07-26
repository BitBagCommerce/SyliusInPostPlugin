<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

class ShipmentBuilder
{
    private ShipmentInterface $shipment;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->shipment = new Shipment();
    }

    public function withShippingMethod(ShippingMethodInterface $shippingMethod): self
    {
        $this->shipment->setMethod($shippingMethod);

        return $this;
    }

    public function withOrder(OrderInterface $order): self
    {
        $this->shipment->setOrder($order);

        return $this;
    }

    public function build(): ShipmentInterface
    {
        return $this->shipment;
    }
}
