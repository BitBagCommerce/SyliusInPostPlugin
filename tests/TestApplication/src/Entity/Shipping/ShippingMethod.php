<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Entity\Shipping;

use BitBag\SyliusInPostPlugin\Entity\ShippingMethodImage;
use BitBag\SyliusInPostPlugin\Model\ShippingMethodImageTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_shipping_method")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_shipping_method')]
class ShippingMethod extends BaseShippingMethod implements ImageAwareInterface
{
    use ShippingMethodImageTrait;

    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: ShippingMethodImage::class, cascade: ['all'])]
    protected ?ImageInterface $image = null;
}
