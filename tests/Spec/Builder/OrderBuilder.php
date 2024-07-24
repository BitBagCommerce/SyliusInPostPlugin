<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
