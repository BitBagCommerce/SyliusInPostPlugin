<?php

namespace BitBag\SyliusInPostPlugin\Entity;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface as BaseShippingExportInterface;

interface ShippingExportInterface extends BaseShippingExportInterface
{
    public function getParcelTemplate(): ?string;

    public function setParcelTemplate(?string $parcel_template): void;
}
