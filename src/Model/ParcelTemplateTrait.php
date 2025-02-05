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
    protected ?string $parcelTemplate = null;

    public function getParcelTemplate(): ?string
    {
        return $this->parcelTemplate;
    }

    public function setParcelTemplate(?string $parcelTemplate): void
    {
        $this->parcelTemplate = $parcelTemplate;
    }
}
