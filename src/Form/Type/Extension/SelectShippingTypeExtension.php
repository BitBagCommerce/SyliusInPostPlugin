<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Form\Type\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectShippingType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectShippingTypeExtension extends AbstractTypeExtension
{
    public const VALIDATION_GROUPS = 'validation_groups';

    public const BITBAG_SELECT_SHIPPING_VALIDATION_GROUP = 'bitbag_select_shipping';

    private array $validationGroups;

    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $this->validationGroups[] = self::BITBAG_SELECT_SHIPPING_VALIDATION_GROUP;

        $resolver->setDefaults([
            self::VALIDATION_GROUPS => $this->validationGroups,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [SelectShippingType::class];
    }
}
