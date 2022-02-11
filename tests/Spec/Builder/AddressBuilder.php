<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\AddressInterface;

class AddressBuilder
{
    private AddressInterface $address;

    public static function create(): AddressBuilder
    {
        return new self();
    }

    private function __construct()
    {
        $this->address = new Address();
    }

    public function withPhoneNumber(string $phoneNumber): AddressBuilder
    {
        $this->address->setPhoneNumber($phoneNumber);

        return $this;
    }

    public function build():AddressInterface
    {
        return $this->address;
    }
}
