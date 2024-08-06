<?php

namespace BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;

interface SelectParcelTemplateActionInterface
{
    public function execute(ShippingExportInterface $shippingExport): void;
}
