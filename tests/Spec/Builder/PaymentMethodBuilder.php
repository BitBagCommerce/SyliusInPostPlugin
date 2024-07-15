<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
