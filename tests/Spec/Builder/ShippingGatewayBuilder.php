<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGateway;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;

class ShippingGatewayBuilder
{
    private ShippingGatewayInterface $shippingGateway;

    public static function create(): ShippingGatewayBuilder
    {
        return new self();
    }

    private function __construct()
    {
        $this->shippingGateway = new ShippingGateway();
    }

    public function withCode(string $code): ShippingGatewayBuilder
    {
        $this->shippingGateway->setCode($code);

        return $this;
    }

    public function build():ShippingGatewayInterface
    {
        return $this->shippingGateway;
    }
}
