<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Entity\Order;

use BitBag\SyliusInPostPlugin\Entity\InPostPoint;
use BitBag\SyliusInPostPlugin\Entity\InPostPointInterface;
use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use BitBag\SyliusInPostPlugin\Model\OrderPointTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Order as BaseOrder;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
class Order extends BaseOrder implements InPostPointsAwareInterface
{
    use OrderPointTrait;

    #[ORM\OneToOne(targetEntity: InPostPoint::class, cascade: ['persist', 'remove', 'refresh'])]
    #[ORM\JoinColumn(name: 'point_id', referencedColumnName: 'id', nullable: true)]
    protected ?InPostPointInterface $point = null;
}
