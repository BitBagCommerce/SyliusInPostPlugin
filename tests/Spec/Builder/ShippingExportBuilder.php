<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Tests\BitBag\SyliusInPostPlugin\Application\src\Entity\ShippingExport;

class ShippingExportBuilder
{
    private ShippingExportInterface $shippingExport;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->shippingExport = new ShippingExport();
    }

    public function withShipment(ShipmentInterface $shipment): self
    {
        $this->shippingExport->setShipment($shipment);

        return $this;
    }

    public function withExternalId(string $id): self
    {
        $this->shippingExport->setExternalId($id);

        return $this;
    }

    public function withShippingGateway(ShippingGatewayInterface $shippingGateway): self
    {
        $this->shippingExport->setShippingGateway($shippingGateway);

        return $this;
    }

    public function build(): ShippingExportInterface
    {
        return $this->shippingExport;
    }
}
