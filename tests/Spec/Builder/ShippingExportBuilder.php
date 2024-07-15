<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

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
