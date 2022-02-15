<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;

class ShippingMethodBuilder
{
    private ShippingMethodInterface $shippingMethod;

    public static function create(): ShippingMethodBuilder
    {
        return new self();
    }

    private function __construct()
    {
        $this->shippingMethod = new ShippingMethod();
    }

    public function withCode(string $code): ShippingMethodBuilder
    {
        $this->shippingMethod->setCode($code);

        return $this;
    }

    public function build(): ShippingMethodInterface
    {
        return $this->shippingMethod;
    }
}
