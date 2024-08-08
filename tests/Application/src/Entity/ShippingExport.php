<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Application\src\Entity;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusInPostPlugin\Model\ParcelTemplateTrait;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport as BaseShippingExport;

class ShippingExport extends BaseShippingExport implements ShippingExportInterface
{
    use ParcelTemplateTrait;
}
