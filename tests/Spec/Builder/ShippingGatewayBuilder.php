<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGateway;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;

class ShippingGatewayBuilder
{
    private ShippingGatewayInterface $shippingGateway;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->shippingGateway = new ShippingGateway();
    }

    public function withCode(string $code): self
    {
        $this->shippingGateway->setCode($code);

        return $this;
    }

    public function build(): ShippingGatewayInterface
    {
        return $this->shippingGateway;
    }
}
