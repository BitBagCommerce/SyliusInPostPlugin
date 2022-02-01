<?php

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Model;

use BitBag\SyliusInPostPlugin\Entity\InPostPointInterface;

interface InPostPointsAwareInterface
{
    public function getPoint(): ?InPostPointInterface;

    public function setPoint(?InPostPointInterface $point): void;
}
