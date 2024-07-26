<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Form\Type\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectPaymentType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectPaymentTypeExtension extends AbstractTypeExtension
{
    public const VALIDATION_GROUPS = 'validation_groups';

    public const BITBAG_SELECT_PAYMENT_VALIDATION_GROUP = 'bitbag_select_payment';

    private array $validationGroups;

    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $this->validationGroups[] = self::BITBAG_SELECT_PAYMENT_VALIDATION_GROUP;

        $resolver->setDefaults([
            self::VALIDATION_GROUPS => $this->validationGroups,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [SelectPaymentType::class];
    }
}
