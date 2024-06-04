<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class HasValidPhoneNumberInPostOrder extends Constraint
{
    public const PHONE_NUMBER_LENGTH_INCORRECT = 'bitbag_sylius_inpost_plugin.order.phone_number.incorrect_length';

    public const PHONE_NUMBER_DEFAULT_LENGTH = 9;

    public const POSSIBLE_PHONE_PREFIXES = [
        '+0048',
        '0048',
        '+48.',
        '+48',
        '48.',
        '48',
    ];

    public function validatedBy(): string
    {
        return 'bitbag_sylius_inpost_plugin_validator_has_valid_phone_number_inpost_order';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
