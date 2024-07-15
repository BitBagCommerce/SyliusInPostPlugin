<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

class OrderBuilder
{
    private OrderInterface $order;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->order = new Order();
    }

    public function withShipment(ShipmentInterface $shipment): self
    {
        $this->order->addShipment($shipment);

        return $this;
    }

    public function withNumber(string $number): self
    {
        $this->order->setNumber($number);

        return $this;
    }

    public function build(): OrderInterface
    {
        return $this->order;
    }
}
