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

    private const INPOST_LOCKER_PARCEL_TEMPLATES = [
        'small', 'medium', 'large',
    ];

    private const INPOST_COURIER_PARCEL_TEMPLATES = [
        'small', 'medium', 'large', 'xlarge',
    ];

    private const INPOST_LOCKER_STANDARD = 'inpost_locker_standard';

    private const INPOST_LOCKER_PASS_THRU = 'inpost_locker_pass_thru';

    private const ERROR_INVALID_PARCEL_TEMPLATE = '"%s" is an invalid parcel template!';

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

        $service = $shippingExport->getShippingGateway()->getConfig()['service'];
        $parcelTemplate = $shippingExport->getParcelTemplate();

        if (in_array($service, [self::INPOST_LOCKER_STANDARD, self::INPOST_LOCKER_PASS_THRU])) {
            $validTemplates = self::INPOST_LOCKER_PARCEL_TEMPLATES;
        } else {
            $validTemplates = self::INPOST_COURIER_PARCEL_TEMPLATES;
        }

        if (!in_array($parcelTemplate, $validTemplates, true)) {
            throw new \Exception(sprintf(self::ERROR_INVALID_PARCEL_TEMPLATE, $parcelTemplate));
        }

        $this->action->execute($shippingExport);
    }
}
