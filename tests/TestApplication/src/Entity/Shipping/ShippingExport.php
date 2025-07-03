<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Entity\Shipping;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusInPostPlugin\Model\ParcelTemplateTrait;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport as BaseShippingExport;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'bitbag_shipping_export')]
class ShippingExport extends BaseShippingExport implements ShippingExportInterface
{
    use ParcelTemplateTrait;

    #[ORM\Column(name: 'parcel_template', type: 'string', nullable: true)]
    protected ?string $parcelTemplate = null;
}
