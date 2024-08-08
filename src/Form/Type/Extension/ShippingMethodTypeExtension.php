<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Form\Type\Extension;

use BitBag\SyliusInPostPlugin\Form\Type\ShippingMethodImageType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ShippingMethodTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', ShippingMethodImageType::class, [
                'label' => false,
                'required' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            ShippingMethodType::class,
        ];
    }
}
