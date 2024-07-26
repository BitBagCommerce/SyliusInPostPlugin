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

class HasAllowedPaymentMethodInPostOrder extends Constraint
{
    public const NOT_ALLOWED_PAYMENT_METHOD_MESSAGE = 'bitbag_sylius_inpost_plugin.order.not_allowed_payment_method';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_inpost_plugin_validator_has_allowed_payment_method_inpost_order';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
