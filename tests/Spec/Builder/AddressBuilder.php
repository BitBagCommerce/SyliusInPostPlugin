<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\AddressInterface;

class AddressBuilder
{
    private AddressInterface $address;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->address = new Address();
    }

    public function withPhoneNumber(string $phoneNumber): self
    {
        $this->address->setPhoneNumber($phoneNumber);

        return $this;
    }

    public function build(): AddressInterface
    {
        return $this->address;
    }
}
