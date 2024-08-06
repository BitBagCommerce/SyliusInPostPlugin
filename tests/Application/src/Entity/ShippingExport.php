<?php

namespace Tests\BitBag\SyliusInPostPlugin\Application\src\Entity;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport as BaseShippingExport;

class ShippingExport extends BaseShippingExport implements ShippingExportInterface
{
    protected $parcel_template;

    public function getParcelTemplate(): ?string
    {
        return $this->parcel_template;
    }

    public function setParcelTemplate(?string $parcel_template): void
    {
        $this->parcel_template = $parcel_template;
    }
}
