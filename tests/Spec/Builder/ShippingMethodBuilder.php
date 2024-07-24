<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;

class ShippingMethodBuilder
{
    private ShippingMethodInterface $shippingMethod;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->shippingMethod = new ShippingMethod();
    }

    public function withCode(string $code): self
    {
        $this->shippingMethod->setCode($code);

        return $this;
    }

    public function build(): ShippingMethodInterface
    {
        return $this->shippingMethod;
    }
}
