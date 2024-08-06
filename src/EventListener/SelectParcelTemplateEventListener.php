<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

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
