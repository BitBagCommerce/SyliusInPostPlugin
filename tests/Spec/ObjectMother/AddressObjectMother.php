<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\ObjectMother;

use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\AddressInterface;

class AddressObjectMother
{
    public static function createSimple(): AddressInterface
    {
        return new Address();
    }

    public static function createWithPhoneNumber(string $phoneNumber = '+48123456789'): AddressInterface
    {
        $address = new Address();
        $address->setPhoneNumber($phoneNumber);

        return $address;
    }
}
