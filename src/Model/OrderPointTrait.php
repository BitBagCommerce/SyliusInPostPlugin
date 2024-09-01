<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Model;

use BitBag\SyliusInPostPlugin\Entity\InPostPointInterface;

trait OrderPointTrait
{
    protected ?InPostPointInterface $point = null;

    public function getPoint(): ?InPostPointInterface
    {
        return $this->point;
    }

    public function setPoint(?InPostPointInterface $point): void
    {
        $this->point = $point;
    }
}
