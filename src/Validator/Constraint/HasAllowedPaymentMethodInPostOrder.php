<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
