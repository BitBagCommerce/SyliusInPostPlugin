<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Entity;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface as BaseShippingExportInterface;

interface ShippingExportInterface extends BaseShippingExportInterface
{
    public function getParcelTemplate(): ?string;

    public function setParcelTemplate(?string $parcelTemplate): void;
}
