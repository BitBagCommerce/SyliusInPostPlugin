<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class PaymentMethodBuilder
{
    private PaymentMethodInterface $paymentMethod;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->paymentMethod = new PaymentMethod();
    }

    public function withCode(string $code): self
    {
        $this->paymentMethod->setCode($code);

        return $this;
    }

    public function build(): PaymentMethodInterface
    {
        return $this->paymentMethod;
    }
}
