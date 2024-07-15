<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class PaymentBuilder
{
    private PaymentInterface $payment;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->payment = new Payment();
    }

    public function withPaymentMethod(PaymentMethodInterface $paymentMethod): self
    {
        $this->payment->setMethod($paymentMethod);

        return $this;
    }

    public function build(): PaymentInterface
    {
        return $this->payment;
    }
}
