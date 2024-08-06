<?php

namespace BitBag\SyliusInPostPlugin\EventListener;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener\SelectParcelTemplateActionInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Webmozart\Assert\Assert;

class SelectParcelTemplateEventListener
{
    private SelectParcelTemplateActionInterface $action;

    public function __construct(
        SelectParcelTemplateActionInterface $action,
    ) {
        $this->action = $action;
    }

    public function setParcelTemplate(ResourceControllerEvent $exportShipmentEvent): void
    {
        /** @var ?ShippingExportInterface $shippingExport */
        $shippingExport = $exportShipmentEvent->getSubject();
        Assert::isInstanceOf($shippingExport, ShippingExportInterface::class);

        $this->action->execute($shippingExport);
    }
}
