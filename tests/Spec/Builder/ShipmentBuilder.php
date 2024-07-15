<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
