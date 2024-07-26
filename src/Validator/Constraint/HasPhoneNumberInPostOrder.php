<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class HasPhoneNumberInPostOrder extends Constraint
{
    public const PHONE_NUMBER_IS_REQUIRED_MESSAGE = 'bitbag_sylius_inpost_plugin.order.phone_number_is_required';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_inpost_plugin_validator_has_phone_number_inpost_order';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
