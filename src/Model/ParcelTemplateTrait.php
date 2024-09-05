<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Model;

trait ParcelTemplateTrait
{
    protected ?string $parcel_template = null;

    public function getParcelTemplate(): ?string
    {
        return $this->parcel_template;
    }

    public function setParcelTemplate(?string $parcel_template): void
    {
        $this->parcel_template = $parcel_template;
    }
}
